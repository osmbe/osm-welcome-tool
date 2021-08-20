<?php

namespace App\Service;

use App\Entity\Changeset;
use App\Repository\ChangesetRepository;
use DateTimeImmutable;
use SimpleXMLElement;

class ChangesetProvider
{
    public function __construct(
        private ChangesetRepository $repository
    ) {
    }

    public function fromOSMCha(array $feature): Changeset
    {
        $changeset = $this->repository->find($feature['id']);
        if (null === $changeset) {
            /** @todo Check https://github.com/brick/geo/blob/master/src/Polygon.php#L199-L208 */
            $extent = [];

            $changeset = new Changeset();
            $changeset->setId($feature['id']);
            $changeset->setCreatedAt(new DateTimeImmutable($feature['properties']['date']));
            $changeset->setComment($feature['properties']['comment']);
            $changeset->setEditor($feature['properties']['editor']);
            $changeset->setLocale($feature['properties']['metadata']['locale'] ?? null);
            $changeset->setChangesCount($feature['properties']['create'] + $feature['properties']['modify'] + $feature['properties']['create']);
            $changeset->setCreateCount($feature['properties']['create']);
            $changeset->setModifyCount($feature['properties']['modify']);
            $changeset->setDeleteCount($feature['properties']['create']);
            $changeset->setExtent($extent);
            // $changeset->setMapper($mapper);
        }

        $changeset->setReasons(array_map(function ($reason): string { return $reason['name']; }, $feature['properties']['reasons']));
        $changeset->setSuspect($feature['properties']['is_suspect']);
        $changeset->setHarmful($feature['properties']['harmful']);
        $changeset->setChecked($feature['properties']['checked']);

        return $changeset;
    }

    public function fromOSM(SimpleXMLElement $element): Changeset
    {
        $attributes = $element->attributes();

        $changeset = $this->repository->find((int) $attributes->id);
        if (null === $changeset) {
            $extent = [
                (float) (self::extractTag($element->tag, 'min_lon')),
                (float) (self::extractTag($element->tag, 'min_lat')),
                (float) (self::extractTag($element->tag, 'max_lon')),
                (float) (self::extractTag($element->tag, 'max_lat')),
            ];

            $changeset = new Changeset();
            $changeset->setId((int) $attributes->id);
            $changeset->setCreatedAt(new DateTimeImmutable((string) $attributes->created_at));
            $changeset->setComment(self::extractTag($element->tag, 'comment') ?? '');
            $changeset->setEditor(self::extractTag($element->tag, 'created_by') ?? '');
            $changeset->setLocale(self::extractTag($element->tag, 'locale'));
            $changeset->setChangesCount((int) ($attributes->changes_count));
            $changeset->setExtent($extent);
            // $changeset->setMapper($mapper);
        }

        return $changeset;
    }

    private static function extractTag(SimpleXMLElement $element, string $key): ?string
    {
        /** @var SimpleXMLElement[] */
        $tags = [];
        foreach ($element as $tag) {
            $tags[] = $tag;
        }
        $filter = array_filter($tags, function (SimpleXMLElement $element) use ($key) {
            $attr = $element->attributes();

            return (string) $attr->k === $key;
        });

        if (0 === \count($filter)) {
            return null;
        }

        $tag = current($filter);
        $attr = $tag->attributes();

        return (string) $attr->v;
    }
}
