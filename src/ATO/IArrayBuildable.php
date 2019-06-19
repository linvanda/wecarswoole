<?php

namespace WecarSwoole\ATO;

interface IArrayBuildable
{
    public function buildFromArray(array $array, bool $strict = true);
}
