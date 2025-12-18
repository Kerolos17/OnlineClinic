# WellClinic Design System

## Color Palette

### Medical Blue (Primary)

-   **50**: `#f0f9ff` - Lightest backgrounds
-   **100**: `#e0f2fe` - Light backgrounds, hover states
-   **200**: `#bae6fd` - Borders, dividers
-   **300**: `#7dd3fc` - Disabled states
-   **400**: `#38bdf8` - Hover states
-   **500**: `#0ea5e9` - Primary brand color
-   **600**: `#0284c7` - Primary buttons, links
-   **700**: `#0369a1` - Text, active states
-   **800**: `#075985` - Dark text
-   **900**: `#0c4a6e` - Darkest text, headers

### Accent Green (Secondary)

-   **50-900**: Mint green shades for success states, CTAs, and accents

## Typography

### Fonts

-   **English**: Inter (Google Fonts)
-   **Arabic**: Cairo (Google Fonts)
-   Both fonts support weights: 300, 400, 500, 600, 700, 800

### Font Classes

-   `.font-inter` - English content
-   `.font-cairo` - Arabic content
-   Applied automatically based on locale

## Components

### Buttons

-   `.btn-primary` - Main action buttons (gradient blue)
-   `.btn-secondary` - Secondary actions (white with border)
-   `.btn-accent` - Success/positive actions (gradient green)

### Cards

-   `.card` - Base card style (white, rounded, shadow)
-   `.card-hover` - Adds hover lift effect
-   `.doctor-card` - Specialized for doctor profiles
-   `.spec-card` - Specialized for specializations

### Forms

-   `.form-input` - Text inputs, selects, textareas
-   `.form-label` - Form field labels
-   `.form-error` - Error messages

### Navigation

-   `.nav-link` - Desktop navigation links
-   `.mobile-nav-link` - Mobile navigation links
-   `.lang-btn` - Language switcher buttons

### Badges & Tags

-   `.badge` - Base badge style
-   `.badge-success` - Green success badge
-   `.badge-warning` - Yellow warning badge
-   `.badge-info` - Blue info badge

### Avatars

-   `.doctor-avatar` - Doctor profile pictures (gradient background)

### Date & Time Selection

-   `.date-btn` - Calendar date buttons
-   `.slot-btn` - Time slot buttons
-   Both support `.selected` state

## Layout Patterns

### Responsive Grid

-   Mobile: 1 column
-   Tablet (md): 2-3 columns
-   Desktop (lg): 3-4 columns

### Spacing

-   Section padding: `py-16` to `py-20`
-   Card padding: `p-6` to `p-8`
-   Element gaps: `gap-4` to `gap-8`

### Container

-   Max width: `max-w-7xl`
-   Horizontal padding: `px-4 sm:px-6 lg:px-8`

## RTL Support

### Automatic Direction

-   HTML `dir` attribute set based on locale
-   Flexbox automatically reverses in RTL

### Manual RTL Adjustments

-   Use conditional classes: `{{ app()->getLocale() == 'ar' ? 'mr-4' : 'ml-4' }}`
-   Rotate icons 180° for RTL: `{{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}`
-   Use `space-x-reverse` for flex containers

## Animations

### Built-in

-   `.animate-float` - Gentle floating motion
-   `.animate-pulse-soft` - Soft pulsing effect
-   `.transition-all` - Smooth transitions on all properties

### Hover Effects

-   Scale: `hover:scale-105`
-   Shadow: `hover:shadow-xl`
-   Translate: `hover:-translate-y-1`

## Icons

### Source

-   Heroicons (inline SVG)
-   Emoji for specializations and decorative elements

### Sizing

-   Small: `w-4 h-4`
-   Medium: `w-5 h-5`
-   Large: `w-6 h-6`

## Accessibility

### Focus States

-   All interactive elements have focus rings
-   Color contrast meets WCAG AA standards
-   Keyboard navigation supported

### Screen Readers

-   Semantic HTML structure
-   ARIA labels where needed
-   Alt text for images

## Best Practices

1. **Always use utility classes** from the design system
2. **Test both languages** (English and Arabic)
3. **Maintain consistent spacing** using Tailwind scale
4. **Use gradients sparingly** for emphasis
5. **Keep animations subtle** for professional feel
6. **Ensure mobile responsiveness** for all components
7. **Use semantic HTML** for better accessibility
