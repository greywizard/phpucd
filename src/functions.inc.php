<?php

namespace Greywizard\Phpucd;

/**
 * @param array|\Traversable $param
 *
 * @return array
 */
function toArray($param)
{
    if (is_array($param)) {
        return $param;
    }

    if (is_object($param) && $param instanceof \Traversable) {
        return iterator_to_array($param);
    }

    throw new \InvalidArgumentException(sprintf(
        'Function %s expects iterable parameter, %s given',
        __FUNCTION__,
        gettype($param)
    ));
}
