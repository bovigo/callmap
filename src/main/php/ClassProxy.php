<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * A class proxy can stub method calls and record all method calls.
 */
interface ClassProxy extends Proxy
{
    /**
     * sets the call map with return values
     *
     * @api
     * @since   3.2.0
     * @param   array  $callMap
     * @return  $this
     * @throws  \InvalidArgumentException  in case any of the mapped methods does not exist or is not applicable
     */
    public function returns(array $callMap): self;

    /**
     * alias for returns()
     *
     * @api
     * @deprecated  since 3.2.0, use returns() instead, will likely be removed with 4.0.0
     * @param   array  $callMap
     * @return  $this
     * @throws  \InvalidArgumentException  in case any of the mapped methods does not exist or is not applicable
     */
    public function mapCalls(array $callMap): self;
}
