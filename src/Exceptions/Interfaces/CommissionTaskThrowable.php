<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions\Interfaces;

interface CommissionTaskThrowable
{
    const EXCEPTION_CODE_BASIC = -1;
    const EXCEPTION_CODE_ARGUMENT = -2;
    const EXCEPTION_CODE_OUT_OF_BOUNDS = -3;
    const EXCEPTION_CODE_KERNEL = -128;
}
