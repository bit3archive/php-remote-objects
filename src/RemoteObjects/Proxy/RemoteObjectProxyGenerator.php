<?php

namespace RemoteObjects\Proxy;

use RemoteObjects\Client;

class RemoteObjectProxyGenerator
{
	public static function generate(Client $client, $interface, $path)
	{
		$class = new \ReflectionClass($interface);

		if (!$class->isInterface()) {
			throw new Exception('You cannot build a virtual proxy from a non-interface!');
		}

		$interfaceName  = $class->getName();
		$shortName      = $class->getShortName();
		$shortProxyName = $shortName . 'Proxy';
		$proxyName      = $class->inNamespace()
			? $class->getNamespaceName() . '\\' . $shortProxyName
			: $shortProxyName;

		if (!class_exists($proxyName)) {
			$code = '';

			if ($class->inNamespace()) {
				$ns = $class->getNamespaceName();

				$code .= <<<EOF
namespace $ns {


EOF;

			}

			$code .= <<<EOF
class $shortProxyName implements \RemoteObjects\RemoteObject, \\{$interfaceName}
{
	/**
	 * @var \RemoteObjects\Client
	 */
	protected \$client;

	function __construct(\$client)
	{
		\$this->client = \$client;
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
					$path
						? $path . '.' . $methodName
						: $methodName,
					true
				);

				$code .= <<<EOF

	) {
		return \$this->client->invokeArgs(
			$escapedName,
			func_get_args()
		);
	}

EOF;

			}

			$code .= <<<EOF
}
EOF;

			if ($class->inNamespace()) {
				$code .= <<<EOF

}
EOF;
			}

			eval($code);
		}

		return new $proxyName($client, null);
	}
}
