<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions\Interfaces;

interface CommissionTaskThrowable
{
    public const EXCEPTION_CODE_BASIC = -1;
    public const EXCEPTION_CODE_ARGUMENT = -2;
    public const EXCEPTION_CODE_OUT_OF_BOUNDS = -3;
    public const EXCEPTION_CODE_KERNEL = -128;
}
