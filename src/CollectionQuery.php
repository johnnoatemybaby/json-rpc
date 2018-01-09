<?php declare(strict_types=1);


namespace Terah\JsonRpc;

use function Terah\Assert\Assert;

use Terah\RestClient\RestClientInterface;
use Terah\RestClient\RestResponseInterface;
use Terah\Types\StructInterface;
use \stdClass;

/**
 * Class CollectionQuery
 *
 * @package Terah\JsonRpc
 */
class CollectionQuery
{
    const STATUS_ACTIVE         = 1;
    const STATUS_INACTIVE       = 0;
    const STATUS_ARCHIVED       = -1;

    /** @var string[] $fields */
    protected $fields    = [];

    /** @var array $where */
    protected $where            = [];

    /** @var integer $limit */
    protected $limit            = 0;

    /** @var integer $limit */
    protected $offset           = 0;

    /** @var string $searchTerm */
    protected $searchTerm       = '';

    /** @var string $orderBy */
    protected $orderBy          = '';

    /** @var string $orderDir */
    protected $orderDir         = '';

    /** @var RestResponseInterface */
    protected $lastResponse     = null;

    public function __construct(array $query=[])
    {
        if ( ! empty($query['_fields']) )
        {
            $this->fields   = explode('|', $query['_fields']);
        }
        unset($query['_fields']);
        if ( ! empty($query['_search']) )
        {
            $this->searchTerm   = (string)$query['_search'];
        }
        unset($query['_search']);
        if ( ! empty($query['_order']) )
        {
            $parts              = explode(',', $query['_order']);
            $direction          = empty($parts[1]) ? 'asc' : $parts[1];
            $this->orderBy    = "{$parts[0]},{$direction}";
        }
        unset($query['_order']);
        if ( ! empty($query['_limit']) )
        {
            $this->limit    = (string)$query['_limit'];
        }
        unset($query['_limit']);
        if ( ! empty($query['_offset']) )
        {
            $this->offset   = (string)$query['_offset'];
        }
        unset($query['_offset']);
        foreach ( $query as $field => $value )
        {
            $this->where[$field]      = (string)$value;
        }
    }

    /**
     * @param array $fields
     * @return CollectionQuery
     */
    protected function select(array $fields=[]) : CollectionQuery
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param array $where
     * @return CollectionQuery
     */
    public function where(array $where=[]) : CollectionQuery
    {
        $this->where = array_merge($this->where, $where);

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereLike(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereLike({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereNotLike(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereNotLike({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereLt(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereLt({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereLte(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereLte({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereGt(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereGt({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value
     * @return CollectionQuery
     */
    public function whereGte(string $field, string $value) : CollectionQuery
    {
        return $this->where([$field => "whereGte({$value})"]);
    }

    /**
     * @param string $field
     * @param string $value1
     * @param string $value2
     * @return CollectionQuery
     */
    public function whereBetween(string $field, string $value1, string $value2) : CollectionQuery
    {
        return $this->where([$field => "whereBetween({$value1},{$value2})"]);
    }

    /**
     * @param string $field
     * @param array $values
     * @return CollectionQuery
     */
    public function whereIn(string $field, array $values) : CollectionQuery
    {
        return $this->where([$field => implode('|', $values)]);
    }

    /**
     * @param string $field
     * @param string $value1
     * @param string $value2
     * @return CollectionQuery
     */
    public function whereNotBetween(string $field, string $value1, string $value2) : CollectionQuery
    {
        return $this->where([$field => "whereNotBetween({$value1},{$value2})"]);
    }

    /**
     * @param string $searchTerm
     * @return CollectionQuery
     */
    public function search(string $searchTerm) : CollectionQuery
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    /**
     * @return CollectionQuery
     */
    public function whereActive() : CollectionQuery
    {
        return $this->whereStatus(self::STATUS_ACTIVE);
    }

    /**
     * @return CollectionQuery
     */
    public function whereInactive() : CollectionQuery
    {
        return $this->whereStatus(self::STATUS_INACTIVE);
    }

    /**
     * @return CollectionQuery
     */
    public function whereArchived() : CollectionQuery
    {
        return $this->whereStatus(self::STATUS_ARCHIVED);
    }

    /**
     * @param int $status
     * @return CollectionQuery
     */
    public function whereStatus(int $status) : CollectionQuery
    {
        Assert($status)->status();

        return $this->where(['status' => $status]);
    }

    /**
     * ORDER BY $columnName (ASC | DESC)
     *
     * @param  string   $columnName - The name of the column or an expression
     * @param  string   $ordering   (DESC | ASC)
     * @return CollectionQuery
     */
    public function orderBy(string $columnName, string $ordering='asc') : CollectionQuery
    {
        Assert(strtolower($ordering))->inArray(['desc', 'asc']);

        $this->orderBy  = $columnName;
        $this->orderDir = strtolower($ordering);

        return $this;
    }

    /**
     * LIMIT $limit
     *
     * @param  int $limit
     * @param  int $offset
     * @return CollectionQuery
     */
    public function limit(int $limit, int $offset=0) : CollectionQuery
    {
        $this->limit = $limit;
        if ( $offset )
        {
            $this->offset($offset);
        }

        return $this;
    }

    /**
     * Return the limit
     *
     * @return int
     */
    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     * OFFSET $offset
     *
     * @param  int      $offset
     * @return CollectionQuery
     */
    public function offset(int $offset) : CollectionQuery
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Return the offset
     *
     * @return int
     */
    public function getOffset() : int
    {
        return $this->offset;
    }

    /**
     * @return CollectionQuery
     */
    public function reset() : CollectionQuery
    {
        $this->fields    = [];
        $this->where            = [];
        $this->searchTerm       = null;
        $this->orderBy          = null;
        $this->orderDir         = null;
        $this->limit            = null;
        $this->offset           = null;

        return $this;
    }

    /**
     * @return string
     */
    public function fetchQueryString() : string
    {
        $query  = $this->fetchQuery();

        return http_build_query($query, '', ini_get('arg_separator.output'), PHP_QUERY_RFC3986);
    }

    /**
     * @return array
     */
    public function fetchQuery() : array
    {
        $query = [];
        if ( ! empty($this->fields) )
        {
            $query['_fields']   = implode('|', $this->fields);
        }
        if ( ! empty($this->searchTerm) )
        {
            $query['_search']   = (string)$this->searchTerm;
        }
        foreach ( $this->where as $field => $value )
        {
            $query[$field]      = (string)$value;
        }
        if ( ! empty($this->orderBy) )
        {
            $direction          = empty($this->orderDir) ? 'asc' : $this->orderDir;
            $query['_order']    = "{$this->orderBy},{$direction}";
        }
        if ( ! empty($this->limit) )
        {
            $query['_limit']    = (string)$this->limit;
        }
        if ( ! empty($this->offset) )
        {
            $query['_offset']   = (string)$this->offset;
        }

        return $query;
    }

    /**
     * @param array $params
     * @return CollectionQuery
     */
    public static function factory(array $params) : CollectionQuery
    {
        return new CollectionQuery($params);
    }
}
