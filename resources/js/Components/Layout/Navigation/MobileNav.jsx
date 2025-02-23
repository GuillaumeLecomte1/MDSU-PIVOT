import { useState } from 'react';
import { usePage } from '@inertiajs/react';
import Link from '@/Components/Common/Link';
import RoleBadge from '@/Components/RoleBadge';
import CartBubble from '@/Components/CartBubble';

export default function MobileNav() {
    const [isOpen, setIsOpen] = useState(false);
    const { auth: { user, permissions } = { user: null, permissions: null } } = usePage().props;

    const isActive = (route) => {
        return route === window.location.pathname;
    };

    return (
        <div className="sm:hidden">
            {/* Hamburger Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
            >
                <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                        className={`${isOpen ? 'hidden' : 'inline-flex'}`}
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                        className={`${isOpen ? 'inline-flex' : 'hidden'}`}
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>

            {/* Mobile Menu */}
            <div className={`${isOpen ? 'block' : 'hidden'} sm:hidden`}>
                <div className="pt-2 pb-3 space-y-1">
                    {/* Public Links */}
                    <Link href={route('home')} active={isActive('home')} className="mobile">
                        Accueil
                    </Link>

                    <Link href={route('products.index')} active={isActive('products.*')} className="mobile">
                        Produits
                    </Link>

                    <Link href={route('categories.index')} active={isActive('categories.*')} className="mobile">
                        Catégories
                    </Link>

                    <Link href={route('ressourceries.index')} active={isActive('ressourceries.*')} className="mobile">
                        Ressourceries
                    </Link>

                    <Link href={route('about')} active={isActive('about')} className="mobile">
                        Notre Histoire
                    </Link>

                    {/* Cart */}
                    <div className="mobile flex items-center justify-between">
                        <span>Panier</span>
                        <CartBubble />
                    </div>
                </div>

                {/* Authenticated Section */}
                {user ? (
                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="flex items-center px-4">
                            <div className="flex-shrink-0">
                                <svg className="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>

                            <div className="ml-3">
                                <div className="font-medium text-base text-gray-800">{user.name}</div>
                                <div className="font-medium text-sm text-gray-500">
                                    <RoleBadge role={user.role} />
                                </div>
                            </div>
                        </div>

                        <div className="mt-3 space-y-1">
                            {/* Role-specific links */}
                            {permissions?.canAccessRessourcerie && (
                                <Link
                                    href={route('ressourcerie.dashboard')}
                                    active={isActive('ressourcerie.*')}
                                    className="mobile text-emerald-600"
                                >
                                    Ma Ressourcerie
                                </Link>
                            )}

                            {permissions?.canAccessAdmin && (
                                <>
                                    <Link
                                        href={route('admin.dashboard')}
                                        active={isActive('admin.*')}
                                        className="mobile text-purple-600"
                                    >
                                        Administration
                                    </Link>
                                    <Link
                                        href={route('admin.documentation.index')}
                                        active={isActive('admin.documentation.*')}
                                        className="mobile text-purple-600"
                                    >
                                        Documentation
                                    </Link>
                                </>
                            )}

                            {/* User Menu Items */}
                            <Link href={route('profile.edit')} className="mobile">
                                Mon Profil
                            </Link>

                            <Link href={route('client.orders.index')} className="mobile">
                                Mes Commandes
                            </Link>

                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="mobile w-full text-left"
                            >
                                Déconnexion
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="space-y-1">
                            <Link href={route('login')} className="mobile">
                                Se connecter
                            </Link>
                            <Link href={route('register')} className="mobile text-emerald-600">
                                S'inscrire
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
} 