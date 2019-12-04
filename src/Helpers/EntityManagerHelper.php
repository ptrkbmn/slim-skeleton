<?php

namespace App\Helpers;

use Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;

use App\Entities\BaseEntity;

class EntityManagerHelper {

  private static $em;

  public static function setEntityManager(EntityManagerInterface $em) {
    self::$em = $em;
  }

  public static function generateAlias(BaseEntity $object, $name) {
    // Lower case, remove non-numeric characters and replace space by a dash.
    $transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', Transliterator::FORWARD);
    $alias = $transliterator->transliterate($name);
    $alias = preg_replace("/\s/", '-', $alias);
    return self::generateUniqueAlias($object, $alias);
  }

  public static function generateUniqueAlias(BaseEntity $object, $alias) {
    $classname = get_class($object);
    // Count aliases
    $query = self::$em->getRepository($classname)
      ->createQueryBuilder('o')
      ->select('o.alias')
      ->where('o.id != ?1')
      ->andWhere('o.alias LIKE ?2')
      ->orderBy("o.id", Criteria::DESC)
      ->setParameter(1, $object->getId())
      ->setParameter(2, "$alias%")
      ->getQuery();

    $result = $query->getArrayResult();
    $count = count($result);
    if($count == 0) {
      return $alias;
    } else {
      $max = $count + 1;
      foreach ($result as $value) {
        if($value["alias"] != $alias && preg_match('/-(\\d+)$/', $value["alias"], $matches) !== FALSE && count($matches) > 0) {
          $max = max($max, $matches[1]);
        }
      }
      return $alias . "-" . ($max + 1);
    }
  }
}