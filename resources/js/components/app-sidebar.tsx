import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { Link } from '@inertiajs/react';
import {
    BarChart3,
    FileText,
    Headphones,
    LayoutGrid,
    MapPin,
    MessageCircle,
    Package,
    ShoppingBag,
    Store,
    Wallet,
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems = [
    {
        title: 'Dashboard',
        href: dashboard().url,
        icon: LayoutGrid,
    },
    {
        title: 'Sipariş Yönetimi',
        href: '#',
        icon: ShoppingBag,
        items: [
            {
                title: 'Siparişler',
                href: '/orders',
            },
            {
                title: 'Kargo Takibi',
                href: '/shipping',
            },
            {
                title: 'Kargo Entegrasyonları',
                href: '/cargo-integrations',
            },
            {
                title: 'İadeler',
                href: '/returns',
            },
        ],
    },
    {
        title: 'Ürün Yönetimi',
        href: '#',
        icon: Package,
        items: [
            {
                title: 'Ürünlerim',
                href: '/products',
            },
            {
                title: 'Ürün Onayları',
                href: '/product-approvals',
            },
            {
                title: 'Kampanyalar',
                href: '/campaigns',
            },
            {
                title: 'Kuponlar',
                href: '/coupons',
            },
            {
                title: 'Import Logları',
                href: '/import-logs',
            },
            {
                title: 'Marka Yetkilendirmeleri',
                href: '/brand-authorizations',
            },
        ],
    },
    {
        title: 'Müşteri İlişkileri',
        href: '#',
        icon: MessageCircle,
        items: [
            {
                title: 'Soru & Cevap',
                href: '/product-questions',
            },
            {
                title: 'Değerlendirmeler',
                href: '/reviews',
            },
            {
                title: 'Takipçiler',
                href: '/followers',
            },
        ],
    },
    {
        title: 'Hesap & Ödeme',
        href: '#',
        icon: Wallet,
        items: [
            {
                title: 'Bakiye',
                href: '/balance',
            },
            {
                title: 'Ödemeler',
                href: '/payments',
            },
            {
                title: 'Faturalar',
                href: '/invoices',
            },
            {
                title: 'Seviyeler',
                href: '/tiers',
            },
        ],
    },
    {
        title: 'Mağaza Sayfası',
        href: '/seller-page',
        icon: Store,
    },
    {
        title: 'Belgeler',
        href: '/vendor-documents',
        icon: FileText,
    },
    {
        title: 'Analytics',
        href: '#',
        icon: BarChart3,
        items: [
            {
                title: 'Analitik',
                href: '/vendor-analytics',
            },
            {
                title: 'SLA Metrikleri',
                href: '/sla-metrics',
            },
            {
                title: 'Günlük İstatistikler',
                href: '/daily-stats',
            },
            {
                title: 'Gelir Raporları',
                href: '/revenue-reports',
            },
        ],
    },
    {
        title: 'Adresler',
        href: '/addresses',
        icon: MapPin,
    },
    {
        title: 'Canlı Destek',
        href: '/support',
        icon: Headphones,
    },
];

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Repository',
//         href: 'https://github.com/laravel/react-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#react',
//         icon: BookOpen,
//     },
// ];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                {/* <NavFooter items={footerNavItems} className="mt-auto" /> */}
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
