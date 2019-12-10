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
     * @param   array<string,mixed>  $callMap
     * @return  $this
     * @throws  \InvalidArgumentException  in case any of the mapped methods does not exist or is not applicable
     */
    public function returns(array $callMap): self;

    /**
     * ensures given methods are stubbed and do not call parent methods
     *
     * @api
     * @since   5.1.0
     * @param   string...  $methods
     * @return  $this
     */
    public function stub(string ...$methods): self;

    /**
     * turn this into a complete stub
     *
     * @internal
     * @return  $this
     */
    public function preventParentCalls(): self;
}
