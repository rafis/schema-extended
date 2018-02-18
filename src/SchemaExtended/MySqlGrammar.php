<?php namespace SchemaExtended;

use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as IlluminateMySqlGrammar;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

/**
 * Extended version of MySqlGrammar with support for additional data types.
 */
class MySqlGrammar extends IlluminateMySqlGrammar {

    /**
     * 
     * @return void
     */
    public function __construct()
    {
        if ( ! in_array('Collate', $this->modifiers) )
        {
            array_splice($this->modifiers, array_search('Unsigned', $this->modifiers) + 1, 0, 'Collate');
        }

        // new versions of Laravel already have comment modifier
        if ( ! in_array('Comment', $this->modifiers) )
        {
            array_splice($this->modifiers, array_search('After', $this->modifiers) - 1, 0, 'Comment');
        }
    }

    /**
     * Get the SQL for a "comment" column modifier.
     *
     * @param \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param \Illuminate\Support\Fluent             $column
     * @return string|null
     */
    protected function modifyCollate(IlluminateBlueprint $blueprint, Fluent $column)
    {
        if ( ! is_null($column->collate) )
        {
            $characterSet = strtok($column->collate, '_');
            return " character set $characterSet collate {$column->collate}";
        }
    }

    /**
     * Get the SQL for a "comment" column modifier.
     *
     * @param \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param \Illuminate\Support\Fluent             $column
     * @return string|null
     */
    protected function modifyComment(IlluminateBlueprint $blueprint, Fluent $column)
    {
        if ( ! is_null($column->comment) )
        {
            $comment = str_replace("'", "\'", $column->comment);
            return " comment '$comment'";
        }
    }

    /**
     * Compile a create table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @param  \Illuminate\Database\Connection  $connection
     * @return string
     */
    public function compileCreate(IlluminateBlueprint $blueprint, Fluent $command, Connection $connection)
    {
        $sql = parent::compileCreate($blueprint, $command, $connection);

        // Table annotation support
        if ( isset($blueprint->comment) )
        {
            $comment = str_replace("'", "\'", $blueprint->comment);
            $sql .= " comment = '$comment'";
        }

        return $sql;
    }

    /**
     * Create the column definition for a binary type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBinary(Fluent $column)
    {
        return "binary({$column->length})";
    }

    /**
     * Create the column definition for a tinytext type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinytext(Fluent $column)
    {
        return "tinytext";
    }

    /**
     * Create the column definition for a tinyblob type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyblob(Fluent $column)
    {
        return "tinyblob";
    }

    /**
     * Create the column definition for a blob type. Corresponds to the core
     * binary type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBlob(Fluent $column)
    {
        return "blob";
    }

    /**
     * Create the column definition for a mediumblob type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumblob(Fluent $column)
    {
        return "mediumblob";
    }

    /**
     * Create the column definition for a longblob type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLongblob(Fluent $column)
    {
        return "longblob";
    }

    /**
     * Create the column definition for an 'set' type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSet(Fluent $column)
    {
        return "set('" . implode("', '", $column->allowed) . "')";
    }

    /**
     * Compile an index creation command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @param  string  $type
     * @return string
     */
    protected function compileKey(IlluminateBlueprint $blueprint, Fluent $command, $type)
    {
        $columns = [];
        foreach($command->columns as $commandColumn)
        {
            foreach($blueprint->getColumns() as $blueprintColumn)
            {
                if ( $blueprintColumn->name != $commandColumn )
                {
                    continue;
                }
                
                $column = $this->wrap($commandColumn);
                if ( isset($command->length) )
                {
                    $column .= "({$command->length})";
                }
                elseif ( 'string' == $blueprintColumn->type && $blueprintColumn->length > 255 )
                {
                    $column .= '(255)';
                }
                
                $columns[] = $column;
            }
        }
        
        $columns = implode(', ', $columns);

        $table = $this->wrapTable($blueprint);

        return "alter table {$table} add {$type} {$command->index}($columns)";
    }

    /**
     * Compile the query to determine if the foreign key exists
     *
     * @return string
     */
    public function compileHasForeign()
    {
        return 'select TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where REFERENCED_TABLE_NAME = ? and CONSTRAINT_NAME = ?';
    }

}
