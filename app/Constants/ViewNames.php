<?php

namespace App\Constants;

/**
 * Class containing all view names as constants.
 *
 * @internal All constants in this class are validated to be valid view names.
 */
class ViewNames
{
    /** @var non-empty-string */
    public const LAYOUTS_APP = 'layouts.app';

    /** @var non-empty-string */
    public const LAYOUTS_GUEST = 'layouts.guest';

    /** @var non-empty-string */
    public const PAYMENT_SUCCESS = 'payment.success';

    /** @var non-empty-string */
    public const PAYMENT_CANCEL = 'payment.cancel';

    /** @var non-empty-string */
    public const PAYMENT_FORM = 'payment.form';

    /** @var non-empty-string */
    public const PAYMENT_CONFIRMATION = 'payment.confirmation';

    /** @var non-empty-string */
    public const STRIPE = 'stripe';
}
