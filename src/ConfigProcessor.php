<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Ruga\Db\Adapter\Adapter;
use Ruga\User\Role\RoleHasRoleTable;
use Ruga\User\Role\RoleTable;

class ConfigProcessor
{
    /**
     * Fills the mezzio-authorization-rbac.roles configuration key with the roles from the database.
     *
     * @param array $config
     *
     * @return array
     * @throws \ReflectionException
     */
    public function __invoke(array $config): array
    {
        try {
            $adapter = new Adapter($config['db']);
            $select=(new Sql($adapter))->select();
            $select->from(['r' => RoleTable::TABLENAME]);
            $select->columns(['client_role' => 'name']);
            $select->join(['rhr' => RoleHasRoleTable::TABLENAME], 'rhr.child_Role_id = r.id', [], Select::JOIN_LEFT);
            $select->join(['pr' => RoleTable::TABLENAME], 'rhr.parent_Role_id = pr.id', ['parent_roles' => new Expression('GROUP_CONCAT(`pr`.`name`)')], Select::JOIN_LEFT);
            $select->group('client_role');
            
            $query=$select->getSqlString($adapter->getPlatform());
//            \Ruga\Log::log_msg("SQL={$query}");
            
            $result=$adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            $roles=[];
            foreach($result as $row) {
                $parentRoles=array_filter(explode(',', $row['parent_roles'] ?? ''));
                
                // Add parent roles if not already defined
                foreach($parentRoles as $parentRole) {
                    if(!array_key_exists($parentRole, $roles)) {
                        $roles[$parentRole]=[];
                    }
                }
                
                $roles[$row['client_role']]=$parentRoles;
            }
            
            $config['mezzio-authorization-rbac']['roles']=$roles;
        } catch (\Throwable $e) {
            \Ruga\Log::addLog($e);
        }
        return $config;
    }
    
}