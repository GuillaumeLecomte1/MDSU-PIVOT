import { Link, usePage, router } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { useState } from 'react';
import Dropdown from '@/Components/Dropdown';
import RoleBadge from '@/Components/RoleBadge';

export default function Navigation() {
    const { auth } = usePage().props;
    const { user, permissions } = auth;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const handleLogout = (e) => {
        e.preventDefault();
        
        if (!confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            return;
        }

        router.post(route('logout'), null, {
            preserveScroll: true,
            onSuccess: () => {
                window.location.href = route('login');
            },
        });
    };

    const isActive = (routeName) => {
        return route().current(routeName);
    };

    const menuItems = [];

    // Ajouter le lien Profile
    menuItems.push(
        <Dropdown.Link key="profile" href={route('profile.edit')}>
            Profile
        </Dropdown.Link>
    );

    // Ajouter les liens de client si l'utilisateur a les permissions
    if (permissions?.canAccessClient) {
        menuItems.push(
            <Dropdown.Link key="client-orders" href={route('client.orders.index')}>
                Mes Commandes
            </Dropdown.Link>
        );
    }

    // Ajouter les liens de ressourcerie si l'utilisateur a les permissions
    if (permissions?.canAccessRessourcerie) {
        menuItems.push(
            <Dropdown.Link key="ressourcerie-dashboard" href={route('ressourcerie.dashboard')}>
                Tableau de bord
            </Dropdown.Link>
        );
    }

    // Ajouter les liens d'administration si l'utilisateur a les permissions
    if (permissions?.canAccessAdmin) {
        menuItems.push(
            <Dropdown.Link key="admin-dashboard" href={route('admin.dashboard')}>
                Administration
            </Dropdown.Link>
        );
        if (permissions?.canManageUsers) {
            menuItems.push(
                <Dropdown.Link key="admin-users" href={route('admin.users.index')}>
                    Gestion Utilisateurs
                </Dropdown.Link>
            );
        }
    }

    // Ajouter le lien de déconnexion
    menuItems.push(
        <Dropdown.Link key="logout" href={route('logout')} method="post" as="button">
            Déconnexion
        </Dropdown.Link>
    );

    return (
        <nav className="bg-[#F2F2F2] border-b border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    <div className="flex">
                        <div className="shrink-0 flex items-center">
                            <Link href="/">
                                <img src="/storage/imagesAccueil/Calque_1.svg" alt="Logo" className="w-24 h-24" />
                                {/* <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" /> */}
                            </Link>
                        </div>

                        <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link
                                href={route('home')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('home') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Accueil
                            </Link>

                            <Link
                                href={route('products.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('products.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Produits
                            </Link>

                            <Link
                                href={route('ressourceries.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('ressourceries.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Ressourceries
                            </Link>

                            <Link
                                href={route('categories.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('categories.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Catégories
                            </Link>

                            {permissions?.canAccessRessourcerie && (
                                <Link
                                    href={route('ressourcerie.dashboard')}
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out font-medium text-emerald-600 hover:text-emerald-700 ${
                                        isActive('ressourcerie.*') ? 'border-emerald-400' : 'border-transparent'
                                    }`}
                                >
                                    Ma Ressourcerie
                                </Link>
                            )}

                            {permissions?.canAccessAdmin && (
                                <Link
                                    href={route('admin.dashboard')}
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out font-medium text-purple-600 hover:text-purple-700 ${
                                        isActive('admin.*') ? 'border-purple-400' : 'border-transparent'
                                    }`}
                                >
                                    Administration
                                </Link>
                            )}
                        </div>
                    </div>

                    <div className="hidden sm:flex sm:items-center sm:ml-6">
                        {user ? (
                            <>
                                <Link
                                    href={route('favorites.index')}
                                    className={`px-3 py-2 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out ${
                                        isActive('favorites.*') ? 'text-gray-900' : ''
                                    }`}
                                >
                                    Mes Favoris
                                </Link>

                                <Link
                                    href={route('cart.index')}
                                    className={`px-3 py-2 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out ${
                                        isActive('cart.*') ? 'text-gray-900' : ''
                                    }`}
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

                                <div className="ml-3 relative">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button type="button" className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                    <div className="flex items-center">
                                                        <span>{user.name}</span>
                                                        <span className="ml-2">
                                                            <RoleBadge role={user.role} />
                                                        </span>
                                                    </div>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>

                                        <Dropdown.Content>
                                            {menuItems}
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            </>
                        ) : (
                            <div className="space-x-4">
                                <Link
                                    href={route('login')}
                                    className="text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out"
                                >
                                    Connexion
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="text-sm font-medium text-emerald-600 hover:text-emerald-500 transition duration-150 ease-in-out"
                                >
                                    Inscription
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </nav>
    );
} 