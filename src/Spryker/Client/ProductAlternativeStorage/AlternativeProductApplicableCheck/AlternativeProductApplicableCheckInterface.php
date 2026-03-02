<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductAlternativeStorage\AlternativeProductApplicableCheck;

use Generated\Shared\Transfer\ProductViewTransfer;

interface AlternativeProductApplicableCheckInterface
{
    public function isAlternativeProductApplicable(ProductViewTransfer $productViewTransfer): bool;
}
