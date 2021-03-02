<?php

namespace OGSoft\LaravelOpenApiValueObject;

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use OGSoft\LaravelOpenApiValueObject\Annotations\Ignore;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use ReflectionType;

trait SchemeTrait
{
	public function getSchema($className, $depth = 2, $inAnnotation = false): ?array
	{

		if ($depth <= 0) {
			return null;
		}

		try {
			$reflectionClass = new ReflectionClass($className);
		} catch (ReflectionException $e) {
			return null;
		}
		if (!$reflectionClass instanceof ReflectionClass) {
			return null;
		}

		$out = [];

		$reader = new AnnotationReader();

		foreach ($reflectionClass->getProperties() as $prop) {
			// skip ignored attributes
			if (!empty($reader->getPropertyAnnotation($prop, Ignore::class))) {
				continue;
			}

			// load annotations if exists
			$annotations = $reader->getPropertyAnnotations($prop);
			if ($this->loadAnnotations($annotations, $prop, $out, $depth, $inAnnotation)) {
				continue;
			}

			// if there are not annotations, try to load prop value
			if (!$this->loadPropertyValue($prop, $reflectionClass, $out, $depth)) {
				// if property do not exists continue
				continue;
			}
		}

		return $out;
	}

	private function loadAnnotations($annotations, ReflectionProperty $prop, &$out, $depth, $inAnnotation): bool
	{
		$return = false;

		if (is_array($annotations)) {
			foreach ($annotations as $annotation) {
				if (class_exists($annotation, true)) {
//          $name = $prop->getName();
//          if (!empty($annotation->getName())) {
//            $name = $annotation->getName();
//          }
					$schema = $annotation->getAnnotation($depth, $inAnnotation);
					$docComment = $prop->getDocComment();
					if (!empty($schema)) {
						$schema = DocParseHelper::addCommentsToSchema($docComment, $schema);
						$out[] = $schema;
					}

					$return = true;
				}
			}
		}

		return $return;
	}

	private function normProp(ReflectionProperty $reflProp): array
	{
		return [lcfirst($reflProp->getName()), "get" . ucfirst($reflProp->getName())];
	}

	/**
	 * @param ReflectionProperty $prop
	 * @param ReflectionClass $reflectionClass
	 * @param array $out
	 * @param $depth
	 * @return bool
	 * @throws ReflectionException
	 */
	private function loadPropertyValue(ReflectionProperty $prop, ReflectionClass $reflectionClass, array &$out, $depth): bool
	{
		$norm = $this->normProp($prop);

		if (empty($norm)) {
			false;
		}

		// getter method name
		$getter = $norm[1];
		// output param name
		$normName = $norm[0];

		$typeObj = null;

		// if exists getter then get return type of this getter
		if (method_exists($reflectionClass->getName(), $getter) && !is_null($returnTypeRef = $reflectionClass->getMethod($getter)->getReturnType())) {
			// php native type
			/** @var $returnTypeRef \ReflectionNamedType */
			if ($returnTypeRef->isBuiltin()) {
				$typeObj = self::generateBuildInSchema($returnTypeRef->getName(), $normName, $returnTypeRef->allowsNull());

				if ($typeObj->type == "array") {
					$s = $this->getArrayType($prop, $reflectionClass);
					$arraySchma = Schema::object();
					if (is_array($s) || is_iterable($s)) {
						$arraySchma = Schema::object()->properties(
							...$s
						);
					} else {
						$arraySchma = Schema::object()->properties(
							$s
						);
					}
					$typeObj = $typeObj->items($arraySchma);
				}
			} // class type
			else {
				// datetime
				if ($returnTypeRef->getName() == DateTime::class || $returnTypeRef->getName() == Carbon::class) {
					$typeObj = Schema::string($normName)->format(Schema::FORMAT_DATE_TIME);
				} // AbstractEntity
				elseif (class_exists($returnTypeRef->getName(), true)) {
					$entityOut = $this->getSchema($returnTypeRef->getName(), $depth - 1);
					if (!empty($entityOut)) {
						$typeObj = Schema::object($normName)->properties(...$entityOut);
					} else {
						$typeObj = null;
					}
				} else {
					throw new Exception("Property has unimplemented return type [" . $reflectionClass->getName() . "::" . $prop->getName() . " - " . $returnTypeRef->getName() . "]");
				}
			}
		} // there is not getter, try to get type from @var docComment
		else {
			$docComment = $prop->getDocComment();
			preg_match("/@var (.*)/", $docComment, $matches);
			$type = $matches[1] ?? null;
			if (empty($type)) {
				throw new Exception("@var definition is missing [" . $reflectionClass->getName() . "::" . $prop->getName() . "]");
			}

			$typeExpl = explode('|', $type);
			$allowNull = sizeof($typeExpl) > 1 && $typeExpl[1] == "null";
			$typeObj = self::generateBuildInSchema($type, $normName, $allowNull);
		}
		$docComment = $prop->getDocComment();
		if (!empty($typeObj)) {
			$typeObj = DocParseHelper::addCommentsToSchema($docComment, $typeObj);
			$out[] = $typeObj;
		}

		return true;
	}

	private static function transformGetterToSetter($getter)
	{
		$pos = strpos($getter, "get");
		if ($pos !== false) {
			$setter = substr_replace($getter, "set", $pos, strlen("set"));
			return $setter;
		} else {
			return null;
		}
	}

	private function getArrayType(ReflectionProperty $prop, ReflectionClass $reflectionClass)
	{
		$norm = $this->normProp($prop);

		$out = Schema::object();

		if (empty($norm)) {
			return $out;
		}
		$getter = $norm[1];
		$setter = self::transformGetterToSetter($getter);
		if (empty($setter)) {
			return $out;
		}

		if (method_exists($reflectionClass->getName(), $setter)) {
			$params = $reflectionClass->getMethod($setter)->getParameters();
			if (sizeof($params) != 1) {
				throw new Exception("Setter " . $reflectionClass->getName() . '::' . $setter . " does not have a parameter");
			}
			$param = $params[0];
			$type = null;
			if ($param->getType() instanceof ReflectionType) {
				$type = $param->getType()->getName();
			}
			if ($param->isVariadic() && (class_exists($type, true))) {
				$out = $this->getSchema($type);
			} else {
				$out = self::generateBuildInSchema($type, $norm[0], $param->allowsNull());
			}
		}
		return $out;
	}

	public static function generateBuildInSchema($typeStr, $normName, $allowNull): Schema
	{
		switch ($typeStr) {
			case "int":
				$typeObj = Schema::integer($normName);
				break;
			case "string":
				$typeObj = Schema::string($normName);
				break;
			case "bool":
				$typeObj = Schema::boolean($normName);
				break;
			case "array":
				$typeObj = Schema::array($normName)->items(Schema::object());
				break;
			default:
				$typeObj = Schema::object($normName);
		}
		if ($allowNull) {
			$typeObj = $typeObj->nullable(true);
		} else {
			$typeObj = $typeObj->nullable(false);
		}

		return $typeObj;
	}
}