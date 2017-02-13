<?php

namespace Blogger\BlogBundle\Entity\Repository;

/**
 * BlogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlogRepository extends \Doctrine\ORM\EntityRepository
{
  public function getLatestBlogs($limit = null)
  {
    $qb = $this->createQueryBuilder('b')
      ->select('b, c')
      ->leftJoin('b.comments', 'c')
      ->addOrderBy('b.created', 'DESC');

    if (false === is_null($limit))
      $qb->setMaxResults($limit);

    return $qb->getQuery()
      ->getResult();
  }

  public function getTags()
  {
    $blogTags = $this->createQueryBuilder('b')
      ->select('b.tags')
      ->getQuery()
      ->getResult();

    $tags = array();
    foreach ($blogTags as $blogTag)
    {
      $tags = array_merge(explode(",", $blogTag['tags']), $tags);
    }

    foreach ($tags as &$tag)
    {
      $tag = trim($tag);
    }

    return $tags;
  }

  public function getTagWeights($tags)
  {
    $tagWeights = array();
    if (empty($tags))
      return $tagWeights;

    foreach ($tags as $tag)
    {
      $tagWeights[$tag] = (isset($tagWeights[$tag])) ? $tagWeights[$tag] + 1 : 1;
    }
    // Shuffle the tags
    uksort($tagWeights, function() {
      return rand() > rand();
    });

    $max = max($tagWeights);

    // Max of 5 weights
    $multiplier = ($max > 5) ? 5 / $max : 1;
    foreach ($tagWeights as &$tag)
    {
      $tag = ceil($tag * $multiplier);
    }

    return $tagWeights;
  }

}
