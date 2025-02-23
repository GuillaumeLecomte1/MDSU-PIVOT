import Link from '@/Components/Common/Link';

export default function DesktopNav() {
    const isActive = (route) => {
        return route === window.location.pathname;
    };

    return (
        <div className="flex items-center justify-center space-x-8">
            <Link href={route('home')} active={isActive('home')}>
                Accueil
            </Link>

            <Link href={route('categories.index')} active={isActive('categories.*')}>
                Catégorie
            </Link>

            <Link href={route('ressourceries.index')} active={isActive('ressourceries.*')}>
                Ressourcerie
            </Link>

            <Link href={route('about')} active={isActive('about')}>
                Notre Histoire
            </Link>
        </div>
    );
} 