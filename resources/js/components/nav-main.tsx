import { ChevronRight, type LucideIcon } from 'lucide-react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useActiveUrl } from '@/hooks/use-active-url';
import { router } from '@inertiajs/react';
import { useRef } from 'react';

interface NavMainProps {
    items: {
        title: string;
        href: string;
        icon?: LucideIcon;
        isActive?: boolean;
        items?: {
            title: string;
            href: string;
        }[];
    }[];
}

export function NavMain({ items }: NavMainProps) {
    const { urlIsActive } = useActiveUrl();
    const scrollPositionRef = useRef<number>(0);

    const handleLinkClick = (href: string, e: React.MouseEvent) => {
        // Mevcut scroll pozisyonunu kaydet
        scrollPositionRef.current = window.scrollY || document.documentElement.scrollTop;
        
        // Eğer aynı sayfadaysa scroll pozisyonunu koru
        if (window.location.pathname === href) {
            e.preventDefault();
            return;
        }

        // Farklı sayfaya gidiyorsa scroll pozisyonunu koru
        router.visit(href, {
            preserveScroll: true,
            onSuccess: () => {
                // Sayfa yüklendikten sonra scroll pozisyonunu restore et
                requestAnimationFrame(() => {
                    window.scrollTo(0, scrollPositionRef.current);
                });
            },
        });
    };

    return (
        <SidebarGroup>
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    // Eğer item'ın altında items varsa collapsible yap
                    if (item.items && item.items.length > 0) {
                        return (
                            <Collapsible
                                key={item.title}
                                asChild
                                defaultOpen={item.isActive}
                                className="group/collapsible"
                            >
                                <SidebarMenuItem>
                                    <CollapsibleTrigger asChild>
                                        <SidebarMenuButton
                                            tooltip={{ children: item.title }}
                                            isActive={urlIsActive(item.href)}
                                        >
                                            {item.icon && <item.icon />}
                                            <span>{item.title}</span>
                                            <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                        </SidebarMenuButton>
                                    </CollapsibleTrigger>
                                    <CollapsibleContent>
                                        <SidebarMenuSub>
                                            {item.items.map((subItem) => (
                                                <SidebarMenuSubItem key={subItem.title}>
                                                    <SidebarMenuSubButton
                                                        asChild
                                                        isActive={urlIsActive(subItem.href)}
                                                    >
                                                        <a
                                                            href={subItem.href}
                                                            onClick={(e) => {
                                                                e.preventDefault();
                                                                handleLinkClick(subItem.href, e);
                                                            }}
                                                        >
                                                            <span>{subItem.title}</span>
                                                        </a>
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            ))}
                                        </SidebarMenuSub>
                                    </CollapsibleContent>
                                </SidebarMenuItem>
                            </Collapsible>
                        );
                    }

                    // Normal item (sub-items yok)
                    return (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                tooltip={{ children: item.title }}
                                isActive={urlIsActive(item.href)}
                            >
                                <a
                                    href={item.href}
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleLinkClick(item.href, e);
                                    }}
                                >
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </a>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
