import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import RoleBadge from '@/Components/RoleBadge';
import CsrfToken from '@/Components/CsrfToken';

export default function AuthenticatedLayout({ header, children }) {
    const { url, props } = usePage();
    const { user, permissions } = props.auth;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const isCurrentRoute = (routeName) => {
        return url.startsWith('/' + routeName);
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <CsrfToken />
            <nav className="bg-white border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <div className="flex space-x-8 sm:-my-px sm:ml-10">
                                <NavLink href={route('home')} active={route().current('home')}>
                                    Accueil
                                </NavLink>
                                <NavLink href={route('products.index')} active={route().current('products.*')}>
                                    Produits
                                </NavLink>
                                <NavLink href={route('ressourceries.index')} active={route().current('ressourceries.*')}>
                                    Ressourceries
                                </NavLink>
                                <NavLink href={route('categories.index')} active={route().current('categories.*')}>
                                    Catégories
                                </NavLink>
                                {permissions.canAccessRessourcerie && (
                                    <NavLink 
                                        href={route('ressourcerie.dashboard')} 
                                        active={route().current('ressourcerie.*')}
                                        className="font-medium text-emerald-600 hover:text-emerald-700"
                                    >
                                        Ma Ressourcerie
                                    </NavLink>
                                )}
                                {permissions.canAccessAdmin && (
                                    <NavLink 
                                        href={route('admin.dashboard')} 
                                        active={route().current('admin.*')}
                                        className="font-medium text-purple-600 hover:text-purple-700"
                                    >
                                        Administration
                                    </NavLink>
                                )}
                            </div>
                        </div>

                        <div className="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
                            <NavLink href={route('favorites.index')} active={route().current('favorites.*')}>
                                Mes Favoris
                            </NavLink>

                            <Link
                                href={route('cart.index')}
                                className="text-gray-500 hover:text-gray-700"
                            >
                                <svg
                                    className="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                            </Link>

                            <Dropdown>
                                <Dropdown.Trigger>
                                    <span className="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                        >
                                            <div className="flex items-center">
                                                <span>{user.name}</span>
                                                <span className="ml-2">
                                                    <RoleBadge role={user.role} />
                                                </span>
                                            </div>

                                            <svg
                                                className="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fillRule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </Dropdown.Trigger>

                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>
                                        Profile
                                    </Dropdown.Link>
                                    {permissions.canAccessRessourcerie && (
                                        <>
                                            <Dropdown.Link href={route('ressourcerie.dashboard')}>
                                                Tableau de bord
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('ressourcerie.products.index')}>
                                                Mes Produits
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('ressourcerie.profile.edit')}>
                                                Profil Ressourcerie
                                            </Dropdown.Link>
                                        </>
                                    )}
                                    {permissions.canAccessAdmin && (
                                        <>
                                            <Dropdown.Link href={route('admin.dashboard')}>
                                                Administration
                                            </Dropdown.Link>
                                            {permissions.canManageUsers && (
                                                <Dropdown.Link href={route('admin.users.index')}>
                                                    Gestion Utilisateurs
                                                </Dropdown.Link>
                                            )}
                                        </>
                                    )}
                                    <Dropdown.Link href={route('logout')} method="post" as="button">
                                        Déconnexion
                                    </Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>

                        <div className="-mr-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
                                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    className="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div className={(showingNavigationDropdown ? 'block' : 'hidden') + ' sm:hidden'}>
                    <div className="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink href={route('home')} active={route().current('home')}>
                            Accueil
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('products.index')} active={route().current('products.*')}>
                            Produits
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('ressourceries.index')} active={route().current('ressourceries.*')}>
                            Ressourceries
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('categories.index')} active={route().current('categories.*')}>
                            Catégories
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('favorites.index')} active={route().current('favorites.*')}>
                            Mes Favoris
                        </ResponsiveNavLink>
                        {permissions.canAccessRessourcerie && (
                            <ResponsiveNavLink href={route('ressourcerie.dashboard')} active={route().current('ressourcerie.*')}>
                                Ma Ressourcerie
                            </ResponsiveNavLink>
                        )}
                        {permissions.canAccessAdmin && (
                            <ResponsiveNavLink href={route('admin.dashboard')} active={route().current('admin.*')}>
                                Administration
                            </ResponsiveNavLink>
                        )}
                    </div>

                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="px-4">
                            <div className="font-medium text-base text-gray-800 flex items-center">
                                <span>{user.name}</span>
                                <span className="ml-2">
                                    <RoleBadge role={user.role} />
                                </span>
                            </div>
                            <div className="font-medium text-sm text-gray-500">{user.email}</div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={route('profile.edit')}>
                                Profile
                            </ResponsiveNavLink>
                            {permissions.canAccessRessourcerie && (
                                <>
                                    <ResponsiveNavLink href={route('ressourcerie.dashboard')}>
                                        Tableau de bord
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink href={route('ressourcerie.products.index')}>
                                        Mes Produits
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink href={route('ressourcerie.profile.edit')}>
                                        Profil Ressourcerie
                                    </ResponsiveNavLink>
                                </>
                            )}
                            {permissions.canAccessAdmin && (
                                <>
                                    <ResponsiveNavLink href={route('admin.dashboard')}>
                                        Administration
                                    </ResponsiveNavLink>
                                    {permissions.canManageUsers && (
                                        <ResponsiveNavLink href={route('admin.users.index')}>
                                            Gestion Utilisateurs
                                        </ResponsiveNavLink>
                                    )}
                                </>
                            )}
                            <ResponsiveNavLink href={route('logout')} method="post" as="button">
                                Déconnexion
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
