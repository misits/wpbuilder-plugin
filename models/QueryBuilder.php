<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class QueryBuilder
{
    const ORDER_ASC = "ASC";
    const ORDER_DESC = "DESC";

    const AND = "and";
    const OR = "or";
    const IN = "in";
    const NOT_IN = "not in";

    private $_postType;
    private $_query;
    private $_queryParams = ["posts_per_page" => -1];
    private $_fetchedParams = null;

    public function __construct(string $postType)
    {
        $this->_postType = $postType;
        $this->_queryParams["post_type"] = $postType::TYPE;
        $this->_queryParams["ignore_sticky_posts"] = 1;
    }

    public static function from(string $postType): self
    {
        return new self($postType);
    }

    public function paginate(int $limit, int $page = 1): self
    {
        $this->_queryParams["posts_per_page"] = $limit;
        $this->_queryParams["paged"] = $page;
        return $this;
    }

    public function where(string $key, $value): self
    {
        $this->_queryParams[$key] = $value;
        return $this;
    }

    public function search($value): self
    {
        return $this->where("s", $value);
    }

    public function where_ids(array $ids, string $operator = self::IN): self
    {
        if ($operator === self::IN) {
            return $this->where("post__in", $ids);
        }

        return $this->where("post__not_in", $ids);
    }

    public function order(string $field, string $order = self::ORDER_ASC): self
    {
        $this->_queryParams["orderby"] = $field;
        $this->_queryParams["order"] = $order;
        return $this;
    }

    public function meta_order(
        string $field,
        string $order = self::ORDER_ASC
    ): self {
        $this->order("meta_value", $order);
        $this->_queryParams["meta_key"] = $field;
        return $this;
    }

    public function meta_query_relation(string $operator): self
    {
        if (!isset($this->_queryParams["meta_query"])) {
            $this->_queryParams["meta_query"] = [];
        }

        $this->_queryParams["meta_query"]["relation"] = $operator;
        return $this;
    }

    public function add_meta_query(
        string $key,
        string $value,
        string $compare = "=",
        string $type = "CHAR"
    ): self {
        if (!isset($this->_queryParams["meta_query"])) {
            $this->_queryParams["meta_query"] = [];
        }

        array_push($this->_queryParams["meta_query"], [
            "key" => $key,
            "value" => $value,
            "compare" => $compare,
            "type" => $type,
        ]);

        return $this;
    }

    public function tax_query_relation(string $operator): self
    {
        if (!isset($this->_queryParams["tax_query"])) {
            $this->_queryParams["tax_query"] = [];
        }

        $this->_queryParams["tax_query"]["relation"] = $operator;
        return $this;
    }

    public function add_tax_query(
        string $taxonomy,
        string $field,
        $terms,
        string $operator = self::IN
    ): self {
        if (!isset($this->_queryParams["tax_query"])) {
            $this->_queryParams["tax_query"] = [];
        }

        array_push($this->_queryParams["tax_query"], [
            "taxonomy" => $taxonomy::TYPE,
            "field" => $field,
            "terms" => $terms,
            "operator" => $operator,
        ]);

        return $this;
    }

    public function after($date, $inclusive = true)
    {
        return $this->add_date_filter(["after" => $date], "", $inclusive);
    }

    public function before($date, $inclusive = true)
    {
        return $this->add_date_filter(["before" => $date], "", $inclusive);
    }

    public function add_date_filter($params, $compare = "", $inclusive = true)
    {
        if (!isset($this->_queryParams["date_query"])) {
            $this->_queryParams["date_query"] = [];
        }

        if ($compare) {
            $params["compare"] = $compare;
        }

        $params["inclusive"] = $inclusive;

        array_push($this->_queryParams["date_query"], $params);

        return $this;
    }

    private function query(): \WP_QUERY
    {
        if (
            !is_null($this->_query) and
            $this->_fetchedParams === $this->_queryParams
        ) {
            return $this->_query;
        }

        $this->_fetchedParams = $this->_queryParams;
        return $this->_query = new \WP_QUERY($this->_queryParams);
    }

    public function find_all(): array
    {
        $postType = $this->_postType;
        return array_map(function ($model) use ($postType) {
            return new $postType($model->ID);
        }, $this->query()->posts);
    }

    public function find_one(): ?PostType
    {
        $models = $this->paginate(1)->find_all();
        return array_shift($models);
    }

    public function limit(int $limit): self
    {
        return $this->paginate($limit);
    }

    public function find_by_id($id): ?PostType
    {
        return $this->where_ids([$id])->find_one();
    }

    public function count_all(): int
    {
        return $this->query()->found_posts;
    }

    public function count_displayed(): int
    {
        return $this->query()->post_count;
    }

    public function page_number(): int
    {
        return $this->query()->max_num_pages;
    }

    public function pagination(): ?string
    {
        return paginate_links([
            "base" => str_replace(99, "%#%", esc_url(get_pagenum_link(99))),
            "format" => "%#%/",
            "total" => $this->page_number(),
            "current" => $this->_queryParams["paged"],
            "type" => "plain",
            "prev_text" => __("«"),
            "next_text" => __("»"),
        ]);
    }
}
