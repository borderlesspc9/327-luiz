import { useCache } from "./cache";
import { useRoute } from "vue-router";

const cache = useCache();

export function useAcl() {
    const route = useRoute();
    
    const isPreviewMode = () => {
        return route.path.startsWith('/preview');
    };
    
    const getUserPermissions = () => {
        // In preview mode, return all permissions
        if (isPreviewMode()) {
            return ['*']; // Special permission that matches all
        }
        
        try {
            const userStr = cache.getUser();
            if (!userStr) return [];
            const user = JSON.parse(userStr);
            const roles = user?.roles || [];
            const permissionsSet = new Set<string>();
            roles.forEach((role: any) => {
                role.permissions.forEach((permission: any) => {
                    permissionsSet.add(permission.name);
                });
            });
            return Array.from(permissionsSet);
        } catch {
            return [];
        }
    }
    
    const hasPermissionTo = (permission: string | Array<string>): boolean => {
        // In preview mode, always return true
        if (isPreviewMode()) {
            return true;
        }
        
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