export function useCache() {
    const tokenKey = 'token';

    const storeItem = (key: string, value: any) => {
        localStorage.setItem(key, value);
    }

    const getItem = (key: string) => {
        return localStorage.getItem(key) || ''
    }

    const clearStorage = () => {
        localStorage.clear();
    }

    const storeToken = (token: string) => {
        storeItem(tokenKey, token);
    }

    const getUser = () => {
        return localStorage.getItem('user') || ''
    }

    const getToken = () => {
        return getItem(tokenKey);
    }

    return {
        storeItem,
        getItem,
        clearStorage,
        storeToken,
        getUser,
        getToken
    }
}