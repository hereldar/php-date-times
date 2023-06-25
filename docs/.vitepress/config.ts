import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  lang: 'en-US',
  title: "Hereldar\\DateTimes",
  description: "Immutable classes to work with dates and times without mixing concepts",
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
    {
      text: 'Guide',
      items: [
        {text: 'Getting Started', link: '/'},
        {text: 'Design philosophy', link: '/design-philosophy'}
      ]
    },
    {
      text: 'Reference',
      activeMatch: '/reference/',
      items: [
        { text: 'DateTime', link: '/reference/date-time' },
        { text: 'LocalDateTime', link: '/reference/local-date-time' },
        { text: 'LocalDate', link: '/reference/local-date' },
        { text: 'LocalTime', link: '/reference/local-time' },
        { text: 'TimeZone', link: '/reference/time-zone' },
        { text: 'Offset', link: '/reference/offset' },
        { text: 'Period', link: '/reference/period' }
      ]
    }
  ]
}

function sidebarGuide() {
  return [
    {text: 'Getting Started', link: '/'},
    {text: 'Design philosophy', link: '/design-philosophy'}
  ]
}

function sidebarReference() {
  return [
    {
      text: 'Reference',
      link: '/reference/',
      items: [
        {
          text: 'Dates and Times',
          items: [
            { text: 'DateTime', link: '/reference/date-time' },
            { text: 'LocalDateTime', link: '/reference/local-date-time' },
            { text: 'LocalDate', link: '/reference/local-date' },
            { text: 'LocalTime', link: '/reference/local-time' }
          ]
        },
        {
          text: 'Time Zones',
          items: [
            { text: 'TimeZone', link: '/reference/time-zone' },
            { text: 'Offset', link: '/reference/offset' }
          ]
        },
        {
          text: 'Durations',
          items: [
            { text: 'Period', link: '/reference/period' }
          ]
        }
      ]
    }
  ]
}
