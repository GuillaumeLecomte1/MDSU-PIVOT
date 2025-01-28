export default function RoleBadge({ role }) {
    const colors = {
        client: 'bg-blue-100 text-blue-800',
        ressourcerie: 'bg-green-100 text-green-800',
        admin: 'bg-purple-100 text-purple-800'
    };

    const labels = {
        admin: 'Admin',
        ressourcerie: 'Ressourcerie',
        client: 'Client'
    };

    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    const colorClasses = colors[role] || colors.client;

    return (
        <span className={`${baseClasses} ${colorClasses}`}>
            {labels[role] || labels.client}
        </span>
    );
} 