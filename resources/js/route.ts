/**
 * Minimal Ziggy-compatible route() helper so Breeze Vue pages work
 * without installing tightenco/ziggy (which would pull extra packages).
 */
type RouteParams = Record<string, string | number> | string | number | undefined;

const namedRoutes: Record<string, string> = {
    login: '/login',
    register: '/register',
    signup: '/signup',
    logout: '/logout',
    dashboard: '/dashboard',
    home: '/',
    'password.request': '/forgot-password',
    'password.email': '/forgot-password',
    'password.store': '/reset-password',
    'password.reset': '/reset-password',
    'password.confirm': '/confirm-password',
    'password.update': '/password',
    'verification.send': '/email/verification-notification',
    'verification.notice': '/verify-email',
    'profile.edit': '/profile',
    'profile.update': '/profile',
    'profile.destroy': '/profile',
    'kyc.store': '/kyc',
    'properties.store': '/properties',
    'admin.approve': '/admin/approve',
    'admin.reject': '/admin/reject',
};

function currentPath(): string {
    const path = window.location.pathname.replace(/\/+$/, '');
    return path === '' ? '/' : path;
}

function currentName(): string | undefined {
    const path = currentPath();
    return Object.keys(namedRoutes).find((name) => namedRoutes[name] === path);
}

export function route(name?: string, _params?: RouteParams, _absolute?: boolean): any {
    if (name === undefined) {
        return {
            current(check?: string) {
                if (!check) {
                    return currentName();
                }
                return currentName() === check;
            },
        };
    }

    const path = namedRoutes[name];
    if (!path) {
        throw new Error(`Unknown route "${name}"`);
    }

    return path;
}

route.current = (check?: string) => route().current(check);
