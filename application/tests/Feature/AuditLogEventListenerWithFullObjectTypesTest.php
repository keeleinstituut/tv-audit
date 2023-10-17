<?php

namespace Tests\Feature;

use App\Enums\EventType;
use App\Enums\ObjectType;

/**
 * Important notes:
 *  * These tests depend on RabbitMQ running and working.
 *  * These tests assume the audit-log-events queue is empty.
 *  ** Queue must be emptied if there are old messages in the way.
 */
class AuditLogEventListenerWithFullObjectTypesTest extends AuditLogEventListenerBaseTest
{
    /** @dataProvider provideAllObjectTypes */
    public function test_remove_object_with_all_object_types(ObjectType $objectType)
    {
        $body = ObjectTypeBasedFullBodyCreators::buildObjectIdentityReferenceMessage(EventType::RemoveObject, $objectType);
        $this->assertEventConsumption($body);
    }

    /** @dataProvider provideAllObjectTypes */
    public function test_create_object_with_all_object_types(ObjectType $objectType)
    {
        $body = ObjectTypeBasedFullBodyCreators::buildObjectIdentityReferenceMessage(EventType::CreateObject, $objectType);
        $this->assertEventConsumption($body);
    }

    /** @dataProvider provideAllObjectTypes */
    public function test_modify_object_with_all_object_types(ObjectType $objectType)
    {
        $body = ObjectTypeBasedFullBodyCreators::buildModifyObjectMessage($objectType);
        $this->assertEventConsumption($body);
    }

    /** @return array<array{ ObjectType }> */
    public static function provideAllObjectTypes(): array
    {
        return collect(ObjectType::cases())
            ->mapWithKeys(fn (ObjectType $objectType) => [$objectType->value => [$objectType]])
            ->all();
    }
}
