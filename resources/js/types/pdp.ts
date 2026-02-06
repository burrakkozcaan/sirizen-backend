/**
 * PDP (Product Detail Page) Type Definitions
 * Trendyol-style dynamic PDP engine types
 */

export interface Badge {
  key: string;
  label: string;
  icon?: string;
  color?: string;
  bg_color?: string;
  text_color?: string;
  priority?: number;
}

export interface ProductImage {
  url: string;
  alt?: string;
}

export interface ProductVariant {
  id: number;
  title: string;
  price: number;
  discount_price?: number;
  stock: number;
  attributes?: Record<string, string>;
  image?: string;
}

export interface ProductAttribute {
  key: string;
  label: string;
  value: string;
  display_value: string;
}

export interface HighlightAttribute {
  key: string;
  label: string;
  value: string;
  display_value: string;
  icon?: string;
  color?: string;
}

export interface SocialProof {
  type: 'cart_count' | 'view_count' | 'sold_count' | 'review_count';
  message: string;
  position: string;
  color?: string;
  icon?: string;
  refresh_interval: number;
}

export interface Product {
  id: number;
  title: string;
  slug: string;
  price: number;
  discount_price?: number;
  discount_percentage: number;
  currency: string;
  rating: number;
  reviews_count: number;
  stock: number;
  is_new: boolean;
  is_bestseller: boolean;
  fast_delivery: boolean;
  images: ProductImage[];
  variants: ProductVariant[];
  attributes: ProductAttribute[];
  description?: string;
  brand?: string;
  category: {
    id: number;
    name: string;
    slug: string;
  };
}

export interface PdpBlockConfig {
  block: string;
  position?: 'main' | 'sidebar' | 'under_title' | 'bottom';
  order?: number;
  visible?: boolean;
  props?: Record<string, unknown>;
}

export interface PdpData {
  product: Product;
  layout: PdpBlockConfig[];
  badges: Badge[];
  highlights: HighlightAttribute[];
  social_proof: SocialProof | null;
  filters: FilterConfig[];
}

export interface FilterConfig {
  key: string;
  label: string;
  type: 'checkbox' | 'range' | 'select' | 'multiselect' | 'color';
  options?: Array<{ value: string; label: string }>;
  is_collapsed: boolean;
  show_count: boolean;
  config?: Record<string, unknown>;
}

export interface PdpBlockProps {
  product: Product;
  badges?: Badge[];
  highlights?: HighlightAttribute[];
  socialProof?: SocialProof | null;
  config?: PdpBlockConfig;
}
