<?php

namespace OGSoft\LaravelOpenApiValueObject;

use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use ReflectionClass;
use ReflectionException;
use function json_decode;

class DocParseHelper
{

	/**
	 * @param $class
	 * @param $method
	 * @return null
	 * @throws Exception
	 */
	public static function getParams($class, $method)
	{
		try {
			$reflectionClass = new ReflectionClass($class);
			$reflectionMethod = $reflectionClass->getMethod($method);
		} catch (ReflectionException $e) {
			throw new Exception("Class or does not exists [" . $class . '::' . $method . "]");
		}

		$docComment = $reflectionMethod->getDocComment();

		$matches = array();
		preg_match("/@params (.*)/", $docComment, $matches);

		if (!empty($matches[1])) {
			$params = json_decode($matches[1]);
			return $params->params;
		}
		return null;
	}

	public static function getComments(string $phpDoc)
	{
		$out = null;
		$lines = explode("\n", $phpDoc);
		foreach ($lines as $line) {
			$line = trim($line);
			$line = str_replace("/**", '', $line);
			$line = str_replace("*/", '', $line);
			$line = str_replace("*", '', $line);
			$line = ltrim($line);
			if (!empty($line) && $line[0] != "@") {
				if (empty($out)) {
					$out = [];
				}
				$out[] = $line;
			}
		}
		return $out;
	}


	public static function getAllParams(string $phpDoc)
	{
		$out = null;
		$lines = explode("\n", $phpDoc);
		foreach ($lines as $line) {
			$line = trim($line);
			$line = str_replace("/**", '', $line);
			$line = str_replace("*/", '', $line);
			$line = str_replace("*", '', $line);
			$line = ltrim($line);
			if (!empty($line) && $line[0] == "@") {
				if (empty($out)) {
					$out = [];
				}
				$expl = explode(' ', $line, 2);
				$out[][$expl[0]] = $expl[1] ?? null;
			}
		}
		return $out;
	}

	public static function addCommentsToSchema(string $phpDoc, Schema $schema): Schema
	{
		$comments = self::getComments($phpDoc);

		if (!empty($comments) && sizeof($comments) > 0) {
			$schema = $schema->title($comments[0]);
			if ((sizeof($comments) > 1)) {
				$schema = $schema->description(implode("\n", array_slice($comments, 1)));
			}
		}

		return $schema;
	}
}