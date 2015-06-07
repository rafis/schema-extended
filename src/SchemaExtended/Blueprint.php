<?php namespace SchemaExtended;

use Illuminate\Support\Fluent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

/**
 * Extended version of Blueprint with
 * support of 'set' data type
 */
class Blueprint extends IlluminateBlueprint {

	/**
	 * Create a new binary column on the table.
	 *
	 * @param  string  $column
	 * @param  int  $length
	 * @return \Illuminate\Support\Fluent
	 */
	public function binary($column, $length = 255)
	{
		return $this->addColumn('binary', $column, compact('length'));
	}

    /**
     * Create a new 'set' column on the table.
     *
     * @param  string  $column
     * @param  array   $allowed
     * @return \Illuminate\Support\Fluent
     */
    public function set($column, array $allowed)
    {
        return $this->addColumn('set', $column, compact('allowed'));
    }

}
