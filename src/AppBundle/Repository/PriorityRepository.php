<?php

namespace AppBundle\Repository;

/**
 * PriorityRepository
 *
 */
class PriorityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Method returns array to form with all priority's in user's _locale
     *
     * @param string $locale user's _locale
     *
     * @return array Formatted array to ChoiceType
     */
    public function getPrioritysInUserLocaleToForm(string $locale): array
    {
        $priority = $this->findBy([], [
                'priorityId' => 'ASC'
        ]);

        $return = [];
        foreach ($priority as $value) {
            $return[$value->{'getPriority'.$locale}()] = $value->getPriorityId();
        }
        return $return;
    }

    /**
     * Method to find one object from priority table in database by id
     *
     * @param int $id Priority's Id in database
     *
     * @return null|object Return Priority object if find in database
     */
    public function findOneByPriorityId(int $id)
    {
        return $this->findOneBy([
            'priorityId' => $id
        ]);
    }
}
?>
