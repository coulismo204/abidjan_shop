<?php

namespace App\Exceptions;

use RuntimeException;

/** Une pointure du panier a été vendue entre-temps. */
class OutOfStock extends RuntimeException {}
