import { Link as InertiaLink } from '@inertiajs/react';

export default function Link({ href, active = false, className = '', method = 'get', as = 'a', children, ...props }) {
    const baseClasses = {
        desktop: 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
        mobile: 'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out'
    };

    const activeClasses = {
        desktop: 'border-indigo-400 text-gray-900',
        mobile: 'border-indigo-400 text-indigo-700 bg-indigo-50 focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700'
    };

    const inactiveClasses = {
        desktop: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
        mobile: 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300'
    };

    const isMobile = className.includes('mobile');
    const type = isMobile ? 'mobile' : 'desktop';

    const classes = `${baseClasses[type]} ${
        active ? activeClasses[type] : inactiveClasses[type]
    } ${className}`;

    return (
        <InertiaLink
            href={href}
            method={method}
            as={as}
            className={classes}
            {...props}
        >
            {children}
        </InertiaLink>
    );
} 