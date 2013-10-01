<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Proxy;

use RemoteObjects\Client;

/**
 * Class RemoteObjectProxyGenerator
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Proxy
 */
class RemoteObjectProxyGenerator
{
	public static function generate(Client $client, $interface, $path)
	{
		$class = new \ReflectionClass($interface);

		$extends = '';
		$implements = 'implements \RemoteObjects\RemoteObject';

		if ($class->isInterface()) {
			$implements .= ', \\' . $class->getName();
		}
		else {
			$extends = 'extends \\' . $class->getName();
		}

		if ($class->inNamespace()) {
			$ns = 'RemoteProxies\\' . RemoteObjectProxy::NS_SEPARATOR . '\\' . $class->getNamespaceName();
		}
		else {
			$ns = 'RemoteProxies\\' . RemoteObjectProxy::NS_SEPARATOR;
		}

		$shortName      = $class->getShortName();
		$shortProxyName = $shortName;
		$proxyName      = $ns . '\\' . $shortProxyName;

		if (!class_exists($proxyName)) {
			$code = '';

			$code .= <<<EOF
namespace $ns {

class $shortProxyName {$extends} {$implements}
{
	/**
	 * @var \RemoteObjects\Client
	 */
	protected \$___client;

	/**
	 * @var string
	 */
	protected \$___path;

	function __construct(\$client, \$path)
	{
		\$this->___client = \$client;
		\$this->___path   = \$path;
	}


EOF;

			foreach ($class->getMethods() as $method) {
				if ($method->isStatic() || !$method->isPublic()) {
					continue;
				}

				$methodName = $method->getName();

				$code .= <<<EOF
	public function {$methodName}(

EOF;

				$parameters = $method->getParameters();

				foreach ($parameters as $index => $parameter) {
					if ($index > 0) {
						$code .= ",\n";
					}

					$temp = '';

					if ($parameter->getClass()) {
						$temp .= '\\' . $parameter->getClass()->getName() . ' ';
					}
					else if ($parameter->isArray()) {
						$temp .= 'array ';
					}
					if ($parameter->isPassedByReference()) {
						$temp .= '&';
					}
					$temp .= '$' . $parameter->getName();
					if ($parameter->isDefaultValueAvailable()) {
						$temp .= '=' . var_export($parameter->getDefaultValue(), true);
					}

					$code .= <<<EOF
		$temp
EOF;
				}

				$escapedName = var_export(
					$methodName,
					true
				);

				$code .= <<<EOF

	) {
		return \$this->___client->invokeArgs(
			\$this->___path
				? \$this->___path . '.' . $escapedName
				: $escapedName,
			func_get_args()
		);
	}

EOF;

			}

			$code .= <<<EOF
}

}
EOF;

			eval($code);
		}

		return new $proxyName($client, $path);
	}
}
