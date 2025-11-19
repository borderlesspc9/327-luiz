import { useCache } from "./cache";

const cache = useCache();

export function useAcl() {
    
    const getUserPermissions = () => {
        const user = JSON.parse(cache.getUser() || '{}');
        
        const roles = user?.roles || [];
        const permissionsSet = new Set<string>();
        roles.forEach((role: any) => {
            role.permissions.forEach((permission: any) => {
                permissionsSet.add(permission.name);
            });
        });

        return Array.from(permissionsSet);
    }
    
    const hasPermissionTo = (permission: string | Array<string>): boolean => {
        const userPermissions = getUserPermissions();

        if (Array.isArray(permission)) {
            return permission.every(p => userPermissions.includes(p));
        }
        return userPermissions.includes(permission);
    }
    
    return {
        getUserPermissions,
        hasPermissionTo
    }
}