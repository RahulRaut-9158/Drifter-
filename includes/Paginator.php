<?php
/**
 * Pagination Helper
 * Standardizes pagination across the application
 */
class Paginator {
    private $page;
    private $limit;
    private $total;
    private $offset;

    public function __construct($page = 1, $limit = 10) {
        $this->page = max(1, (int)$page);
        $this->limit = max(1, min((int)$limit, 100)); // Max 100 items per page
        $this->offset = ($this->page - 1) * $this->limit;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getPage() {
        return $this->page;
    }

    public function setTotal($total) {
        $this->total = (int)$total;
    }

    public function getLimitOffset() {
        return "LIMIT {$this->limit} OFFSET {$this->offset}";
    }

    public function getMetadata() {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
            'pages' => ceil($this->total / $this->limit),
            'has_more' => $this->page < ceil($this->total / $this->limit)
        ];
    }
}
?>
