<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Raised when a sale cannot be completed for a reason the cashier can act on,
 * such as an empty cart line or insufficient stock. The message is shown to
 * the user, so it must never carry internal detail.
 */
class CheckoutException extends RuntimeException
{
}
