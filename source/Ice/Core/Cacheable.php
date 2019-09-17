<?php namespace Ice\Core;

interface Cacheable
{
    /**
     * Validate cacheable object
     *
     * @param  $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function validate($value);

    /**
     * Invalidate cacheable object
     *
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function invalidate();
}
