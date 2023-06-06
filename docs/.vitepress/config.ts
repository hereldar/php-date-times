import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  lang: 'en-US',
  title: "PHP DateTimes",
  description: "Immutable classes for dates, times, time-zones and periods",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: nav(),
    sidebar: {
      '/': sidebarGuide(),
      '/reference/': sidebarReference()
    },
    search: {
      provider: 'local'
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/hereldar/php-date-times' }
    ]
  },
  base: '/php-date-times/'
})

function nav() {
  return [
    { text: 'Guide', link: '/' },
    {
      text: 'Reference',
      activeMatch: '/reference/',
      items: [
        { text: 'LocalDate', link: '/reference/local-date' }
      ]
    }
  ]
}

function sidebarGuide() {
  return [
    {text: 'Guide', link: '/'}
  ]
}

function sidebarReference() {
  return [
    {
      text: 'Reference',
      items: [
        { text: 'LocalDate', link: '/reference/local-date' }
      ]
    }
  ]
}
