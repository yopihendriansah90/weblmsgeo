---
name: Academic Clarity
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#464555'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#777587'
  outline-variant: '#c7c4d8'
  surface-tint: '#4d44e3'
  primary: '#3525cd'
  on-primary: '#ffffff'
  primary-container: '#4f46e5'
  on-primary-container: '#dad7ff'
  inverse-primary: '#c3c0ff'
  secondary: '#006a61'
  on-secondary: '#ffffff'
  secondary-container: '#86f2e4'
  on-secondary-container: '#006f66'
  tertiary: '#684000'
  on-tertiary: '#ffffff'
  tertiary-container: '#885500'
  on-tertiary-container: '#ffd4a4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2dfff'
  primary-fixed-dim: '#c3c0ff'
  on-primary-fixed: '#0f0069'
  on-primary-fixed-variant: '#3323cc'
  secondary-fixed: '#89f5e7'
  secondary-fixed-dim: '#6bd8cb'
  on-secondary-fixed: '#00201d'
  on-secondary-fixed-variant: '#005049'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.25'
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: '1.3'
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.2'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  container-max: 1280px
  gutter: 24px
  margin-desktop: 40px
  margin-mobile: 16px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
---

## Brand & Style

The brand personality is **Academic Clarity**: an environment that feels like a high-end digital campus. It is designed to be the "calm within the storm" for students, reducing cognitive load while maintaining an energetic, forward-thinking momentum. 

The design style follows a **Modern Corporate** aesthetic infused with **Soft Minimalist** elements. It prioritizes high-quality typography and generous whitespace to prevent "dashboard fatigue." Key interactions use subtle tactile feedback to make the interface feel responsive and encouraging. The target audience is modern learners who value efficiency, focus, and a professional yet inviting digital workspace.

## Colors

The palette is anchored by **Indigo-600** (#4F46E5), serving as the primary brand color to represent trust, depth, and institutional stability. It is used for primary actions, navigation states, and progress indicators.

**Secondary Teal** (#0D9488) is utilized for "Success" states, completed modules, and collaborative features, offering a refreshing contrast to the primary blue. **Tertiary Orange** (#F59E0B) is reserved for "Active" or "Attention" states, such as upcoming deadlines or reminders, providing warmth without inducing anxiety.

The **Neutral** palette is a cool Slate (#64748B), used to maintain a clean hierarchy. Backgrounds should utilize a very light grey-blue (Slate-50) to reduce eye strain during long study sessions.

## Typography

This design system uses a dual-font approach to balance personality with extreme readability. **Plus Jakarta Sans** is used for headlines; its slightly rounded, geometric nature feels approachable and modern. **Inter** is the workhorse for body copy and labels, chosen for its exceptional legibility at small sizes and high x-height, which is critical for reading long-form course content.

Vertical rhythm is strictly maintained with a 1.6x line height for body text to ensure a comfortable reading pace. Headlines use tighter leading and negative letter-spacing to appear cohesive and authoritative.

## Layout & Spacing

The layout utilizes a **12-column fluid grid** for desktop and a **4-column grid** for mobile. The philosophy is "Content First," where the main learning area is prioritized, and supplementary tools (navigation, chat, notes) are tucked into collapsible sidebars or drawers.

A strict **8px spacing scale** governs all margins and padding. Page layouts should feature generous top-margins (Stack-LG) to give the content room to breathe. On mobile, margins reduce to 16px to maximize the available screen real estate for reading and video players.

## Elevation & Depth

Visual hierarchy is established through **Tonal Layers** and **Ambient Shadows**. 

1.  **Level 0 (Base):** The page background, using a subtle off-white or Slate-50.
2.  **Level 1 (Cards):** White surfaces with a very soft, diffused shadow (0px 4px 20px rgba(0,0,0,0.05)) to separate learning modules from the background.
3.  **Level 2 (Active/Hover):** Enhanced shadows (0px 8px 30px rgba(79, 70, 229, 0.1)) used when a student interacts with a course card or sidebar item.
4.  **Level 3 (Modals):** High-contrast overlays with backdrop blurs (8px) to focus attention on critical tasks like quizzes or settings.

## Shapes

The design system employs **Rounded (0.5rem)** corners as the standard. This choice reflects the "approachable" brand pillar, removing the harshness of sharp edges to create a softer, more inviting interface. 

Buttons and input fields use the 0.5rem radius, while larger containers (like course cards or video players) scale up to **1rem (rounded-lg)** to emphasize their status as primary content containers. Status chips and badges should utilize the **Pill** shape to distinguish them from interactive buttons.

## Components

### Buttons
Primary buttons use a solid Indigo fill with white text. Hover states should involve a slight darkening of the fill and a subtle lift via shadow. Secondary buttons use a ghost style with an Indigo border or a light Slate background.

### Cards
Course and assignment cards are the primary navigational units. They must feature a 1px border (#E2E8F0) and a subtle Level 1 shadow. On hover, the border color transitions to the Primary Indigo.

### Inputs
Text fields use a 16px font size (Inter) to prevent iOS zoom issues. They feature a light grey border that transforms into a 2px Indigo border on focus. Placeholders should be clearly distinguished from user-inputted text.

### Progress Indicators
Progress bars should use a 8px height with fully rounded caps. The track is a light grey (Slate-100), and the progress fill is the Primary Indigo or Secondary Teal for completed states.

### Course Navigation (Sidebar)
The sidebar uses a clean, vertical list. Active items are highlighted with a thick left-border (4px) in Primary Indigo and a subtle background tint (Indigo-50). Icons should be line-based and 24px in size for clarity.