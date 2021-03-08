<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTest
 * These test mainly exist to avoid surprises during vendor upgrades
 *
 * @package AbterPhp\Framework\Authorization
 */
class CasbinTest extends TestCase
{
    /**
     * Example from https://github.com/php-casbin/casbin-tutorials/blob/master/tutorials/Get-Started.md
     *
     * @throws CasbinException
     */
    public function testAcl(): void
    {
        $enforcer = new Enforcer(__DIR__ . "/fixtures/acl_model.conf", __DIR__ . "/fixtures/acl_policy.csv");

        $this->assertTrue($enforcer->enforce('alice', 'data1', 'read'));
        $this->assertFalse($enforcer->enforce('alice', 'data2', 'write'));

        $this->assertFalse($enforcer->enforce('bob', 'data1', 'read'));
        $this->assertTrue($enforcer->enforce('bob', 'data2', 'write'));
    }

    /**
     * Example from https://github.com/php-casbin/casbin-tutorials/blob/master/tutorials/ABAC-with-Casbin.md
     *
     * @throws CasbinException
     */
    public function testAbac(): void
    {
        $enforcer = new Enforcer(__DIR__ . "/fixtures/abac_model.conf");

        $data1        = new \stdClass();
        $data1->name  = 'data1';
        $data1->owner = 'alice';

        $data2        = new \stdClass();
        $data2->name  = 'data2';
        $data2->owner = 'bob';

        $this->assertTrue($enforcer->enforce('alice', $data1, 'read'));
        $this->assertFalse($enforcer->enforce('alice', $data2, 'write'));

        $this->assertFalse($enforcer->enforce('bob', $data1, 'read'));
        $this->assertTrue($enforcer->enforce('bob', $data2, 'write'));
    }

    /**
     * Example from https://github.com/php-casbin/casbin-tutorials/blob/master/tutorials/RBAC-with-Casbin.md
     *
     * @throws CasbinException
     */
    public function testRbac(): void
    {
        $enforcer = new Enforcer(__DIR__ . "/fixtures/rbac_model.conf");

        // alice has the admin role
        $enforcer->addRoleForUser('alice', 'admin');
        // bob has the member role
        $enforcer->addRoleForUser('bob', 'member');

        $enforcer->addPermissionForUser('member', '/foo', 'GET');
        $enforcer->addPermissionForUser('member', '/foo/:id', 'GET');

        // admin inherits all permissions of member
        $enforcer->addRoleForUser('admin', 'member');

        $enforcer->addPermissionForUser('admin', '/foo', 'POST');
        $enforcer->addPermissionForUser('admin', '/foo/:id', 'PUT');
        $enforcer->addPermissionForUser('admin', '/foo/:id', 'DELETE');

        $data1        = new \stdClass();
        $data1->name  = 'data1';
        $data1->owner = 'alice';

        $data2        = new \stdClass();
        $data2->name  = 'data2';
        $data2->owner = 'bob';

        // Alice is an admin, so she can do everything that was already set up
        $this->assertTrue($enforcer->enforce('alice', '/foo', 'GET'));
        $this->assertTrue($enforcer->enforce('alice', '/foo', 'GET'));
        $this->assertTrue($enforcer->enforce('alice', '/foo', 'POST'));
        $this->assertTrue($enforcer->enforce('alice', '/foo/1', 'PUT'));
        $this->assertTrue($enforcer->enforce('alice', '/foo/1', 'DELETE'));

        // Stuff that has not been set up, Alice will still not be allowed to do
        $this->assertFalse($enforcer->enforce('alice', '/foo/1', 'PATCH'));
        $this->assertFalse($enforcer->enforce('alice', '/bar', 'GET'));

        // Bob is just a member, so he can not do everything that was already set up
        $this->assertTrue($enforcer->enforce('bob', '/foo', 'GET'));
        $this->assertTrue($enforcer->enforce('bob', '/foo', 'GET'));
        $this->assertFalse($enforcer->enforce('bob', '/foo', 'POST'));
        $this->assertFalse($enforcer->enforce('bob', '/foo/1', 'PUT'));
        $this->assertFalse($enforcer->enforce('bob', '/foo/1', 'DELETE'));
    }

    /**
     * Example from https://casbin.org/docs/en/rbac-with-domains
     *
     * @throws CasbinException
     */
    public function testRbacWithDomains(): void
    {
        $enforcer = new Enforcer(
            __DIR__ . "/fixtures/rbac_with_domains_model.conf",
            __DIR__ . "/fixtures/rbac_with_domains_policy.csv",
        );

        // Alice is an Admin in domain1 and a user in domain2
        $this->assertTrue($enforcer->enforce('alice', 'domain1', 'data1', 'read'));
        $this->assertTrue($enforcer->enforce('alice', 'domain1', 'data1', 'write'));
        $this->assertTrue($enforcer->enforce('alice', 'domain2', 'data2', 'read'));
        $this->assertFalse($enforcer->enforce('alice', 'domain2', 'data2', 'write'));

        // Bob is an Admin in domain2 only
        $this->assertFalse($enforcer->enforce('bob', 'domain1', 'data1', 'read'));
        $this->assertFalse($enforcer->enforce('bob', 'domain1', 'data1', 'write'));
        $this->assertTrue($enforcer->enforce('bob', 'domain2', 'data2', 'read'));
        $this->assertTrue($enforcer->enforce('bob', 'domain2', 'data2', 'read'));
    }

    /**
     * Example from https://casbin.org/docs/en/priority-model
     *
     * @throws CasbinException
     */
    public function testPriority(): void
    {
        $enforcer = new Enforcer(
            __DIR__ . "/fixtures/priority_model.conf",
            __DIR__ . "/fixtures/priority_policy.csv",
        );

        // Alice is allowed access, but is in data1_deny_group, which has higher priority
        $this->assertFalse($enforcer->enforce('alice', 'data1', 'read'));
        $this->assertFalse($enforcer->enforce('alice', 'data1', 'write'));
        $this->assertFalse($enforcer->enforce('alice', 'data2', 'read'));
        $this->assertFalse($enforcer->enforce('alice', 'data2', 'write'));
        // Bob is denied access, but is in data2_allow_group, which has higher priority
        $this->assertTrue($enforcer->enforce('bob', 'data2', 'read'));
        $this->assertTrue($enforcer->enforce('bob', 'data2', 'write'));
        $this->assertFalse($enforcer->enforce('bob', 'data1', 'read'));
        $this->assertFalse($enforcer->enforce('bob', 'data1', 'write'));
    }
}
