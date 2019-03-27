<?php
namespace MODEL;

/**
 * Class InfoModel
 * @package MODEL
 * @author Christian Vinding Rasmussen
 * InfoModel is an model for accessing the news, about section and products of the website.
 * It is also used for adding, editing and deleting that information.
 */
class InfoModel extends Model {

    /**
     * getNews() is used for selecting all the news from the table website_news
     * @param int $titleCount
     * @param int $bodyCount
     * @param int $limit
     * @param string $webDomain
     * @param int $internal
     * @return array
     */
    public function getNews(int $titleCount, int $bodyCount, int $limit, string $webDomain, int $internal) : array {
        $db = new \DATABASE\Database();

        $query = "SELECT id, SUBSTRING(news.title, 1, :titleCount) title, SUBSTRING(news.description, 1, :bodyCount) description, news.author, news.updated FROM website_news news WHERE internal = :internal AND (SELECT web.id FROM company_web_domains web WHERE web.name = :web_domain) = news.web_domain ORDER BY id DESC";

        $values = ["titleCount" => $titleCount, "bodyCount" => $bodyCount, "internal" => $internal, "web_domain" => $webDomain];

        if($limit !== 0) {
            $query .= " LIMIT :limit";
            $values["limit"] = $limit;
        }

        $data = $db->query($query, $values)
            ->fetchArray();

        return $data;
    }

    /**
     * getAbout() is used for selecting the about from the table website_about
     * @param string $webDomain
     * @return array
     */
    public function getAbout(string $webDomain) : array {
        $db = new \DATABASE\Database();

        $data = $db->query("SELECT about.description, about.author, about.updated FROM website_about about WHERE (SELECT id FROM company_web_domains web WHERE web.name = :web_domain) = about.web_domain LIMIT 1", ["web_domain" => $webDomain])
            ->fetchArray();

        return $data;
    }

    /**
     * getProducts() is used for selecting the products from the table website_products
     * @param string $webDomain
     * @return array
     */
    public function getProducts(string $webDomain) : array {
        $db = new \DATABASE\Database();

        $data = $db->query("SELECT product.title, product.description, product.author, product.updated FROM website_products product WHERE (SELECT id FROM company_web_domains web WHERE web.name = :web_domain) = product.web_domain", ["web_domain" => $webDomain])
            ->fetchArray();

        return $data;
    }

    public function createNews(string $title, string $description, int $internal, string $webDomain, string $author) : bool {
        $db = new \DATABASE\Database();

        $values = ["title" => $title, "description" => $description, "internal" => $internal, "web_domain" => $webDomain, "author" => $author];

        $result = $db->query("INSERT INTO website_news (title, description, internal, web_domain, author) VALUES (:title, :description, :internal, (SELECT id FROM company_web_domains WHERE name = :web_domain), :author)", $values)
            ->affectedRows();

        //$data = $db->query("INSERT INTO website_news (title, description, internal)");
        return (bool) $result;
    }

}