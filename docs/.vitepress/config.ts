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
    outline: [2, 3],
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
        { text: 'DateTime', link: '/reference/date-time' },
        { text: 'LocalDate', link: '/reference/local-date' },
        { text: 'LocalDateTime', link: '/reference/local-date-time' },
        { text: 'LocalTime', link: '/reference/local-time' },
        { text: 'TimeZone', link: '/reference/time-zone' },
        { text: 'Offset', link: '/reference/offset' }
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
        { text: 'DateTime', link: '/reference/date-time' },
        { text: 'LocalDate', link: '/reference/local-date' },
        { text: 'LocalDateTime', link: '/reference/local-date-time' },
        { text: 'LocalTime', link: '/reference/local-time' },
        { text: 'TimeZone', link: '/reference/time-zone' },
        { text: 'Offset', link: '/reference/offset' }
      ]
    }
  ]
}
