import Link from '@/Components/Common/Link';
import { usePage } from '@inertiajs/react';
import RoleBadge from '@/Components/RoleBadge';
import Dropdown from '@/Components/Dropdown';
import DocLink from '@/Components/Documentation/DocLink';

export default function DesktopNav() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const permissions = auth?.permissions || {};

    // Debug logs with distinctive markers
    console.log('🔍 DesktopNav COMPONENT - START LOGS 🔍');
    console.log('📱 DesktopNav - Auth:', auth);
    console.log('👤 DesktopNav - User:', user);
    console.log('🔑 DesktopNav - Permissions:', permissions);
    console.log('👮 DesktopNav - Can Access Admin:', permissions.canAccessAdmin);
    console.log('🎭 DesktopNav - Current Role:', user?.role);
    console.log('🔍 DesktopNav COMPONENT - END LOGS 🔍');

    const isActive = (route) => {
        return route === window.location.pathname;
    };

    return (
        <div className="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
            {/* Public Links */}
            <Link href={route('home')} active={isActive('home')}>
                Accueil
            </Link>

            <Link href={route('products.index')} active={isActive('products.*')}>
                Produits
            </Link>

            <Link href={route('categories.index')} active={isActive('categories.*')}>
                Catégories
            </Link>

            <Link href={route('ressourceries.index')} active={isActive('ressourceries.*')}>
                Ressourceries
            </Link>

            <Link href={route('about')} active={isActive('about')}>
                Notre Histoire
            </Link>

            {/* Authenticated Links */}
            {user ? (
                <div className="flex items-center space-x-4">
                    {/* Role-specific links */}
                    {permissions?.canAccessRessourcerie && (
                        <Link
                            href={route('ressourcerie.dashboard')}
                            active={isActive('ressourcerie.*')}
                            className="text-emerald-600 hover:text-emerald-700"
                        >
                            Ma Ressourcerie
                        </Link>
                    )}

                    {/* Admin Links */}
                    {permissions?.canAccessAdmin && (
                        <>
                            <DocLink
                                className="text-blue-600 hover:text-blue-700"
                            />

                            <Link
                                href={route('admin.dashboard')}
                                active={isActive('admin.*')}
                                className="text-purple-600 hover:text-purple-700"
                            >
                                Administration
                            </Link>
                        </>
                    )}

                    {/* User Menu */}
                    <Dropdown>
                        <Dropdown.Trigger>
                            <button type="button" className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div className="flex items-center">
                                    <span>{user.name}</span>
                                    <span className="ml-2">
                                        <RoleBadge role={user.role} />
                                    </span>
                                </div>
                            </button>
                        </Dropdown.Trigger>

                        <Dropdown.Content>
                            <Dropdown.Link href={route('profile.edit')}>
                                Mon Profil
                            </Dropdown.Link>
                            {permissions?.canAccessAdmin && (
                                <>
                                    <Dropdown.Link href={route('admin.dashboard')}>
                                        Administration
                                    </Dropdown.Link>
                                    <Dropdown.Link href={route('admin.documentation.index')}>
                                        Documentation
                                    </Dropdown.Link>
                                </>
                            )}
                            <Dropdown.Link href={route('logout')} method="post" as="button">
                                Déconnexion
                            </Dropdown.Link>
                        </Dropdown.Content>
                    </Dropdown>

                    {/* Cart Icon */}
                    <Link href={route('cart.index')} className="text-gray-500 hover:text-gray-700">
                        <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </Link>
                </div>
            ) : (
                <div className="flex items-center space-x-4">
                    <Link href={route('login')} className="text-gray-500 hover:text-gray-700">
                        Se connecter
                    </Link>
                    <Link href={route('register')} className="text-emerald-600 hover:text-emerald-500">
                        S'inscrire
                    </Link>
                </div>
            )}
        </div>
    );
} 