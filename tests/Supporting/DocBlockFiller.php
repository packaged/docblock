<?php

namespace Packaged\DocBlock\Tests\Supporting;

/**
 * This class is for a test
 *
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ac ligula risus.
 *
 * @description This is a test class
 */
class DocBlockFiller
{
  /**
   * This property is for data
   *
   * @index
   * @name Hello
   *
   * @value 1
   * @value 2
   * @value 3
   */
  public $property;

  public $propertyTwo;

  /** @var integer */
  public $propertyThree;

  /* normal comment */
  public $propertyFour;

  /**
   * Method Description
   *
   * @return mixed
   */
  public function getProperty()
  {
    return $this->property;
  }
}
