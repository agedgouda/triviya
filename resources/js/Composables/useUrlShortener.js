import { ref, onMounted } from 'vue'

export function useUrlShortener() {
  const STORAGE_KEY = 'vue-url-shortener-v1'
  const BASE_URL = window.location.origin + '/'

  const items = ref([])

  function load() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY)
      items.value = raw ? JSON.parse(raw) : []
    } catch (e) {
      items.value = []
    }
  }

  function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items.value))
  }

  function validateAlias(alias) {
    if (!alias) return true
    return /^[A-Za-z0-9-_]+$/.test(alias)
  }

  function generateSlug(length = 6) {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    return Array.from({ length }, () => chars.charAt(Math.floor(Math.random() * chars.length))).join('')
  }

  function create({ longUrl, customAlias }) {
    if (!longUrl) throw new Error('Please enter a URL.')

    try {
      new URL(longUrl)
    } catch {
      throw new Error('Invalid URL.')
    }

    if (!validateAlias(customAlias)) {
      throw new Error('Alias contains invalid characters.')
    }

    const alias = customAlias?.trim()
    let slug = alias || generateSlug()

    if (items.value.find(i => i.slug === slug)) {
      throw new Error('That alias is already taken.')
    }

    const item = {
      slug,
      longUrl,
      createdAt: new Date().toISOString(),
      fullShortUrl: BASE_URL + slug,
    }

    items.value.unshift(item)
    save()
    return item
  }

  function remove(slug) {
    items.value = items.value.filter(i => i.slug !== slug)
    save()
  }

  function copy(text) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text)
    } else {
      const ta = document.createElement('textarea')
      ta.value = text
      document.body.appendChild(ta)
      ta.select()
      document.execCommand('copy')
      ta.remove()
    }
  }

  function formatDate(iso) {
    try {
      return new Date(iso).toLocaleString()
    } catch {
      return iso
    }
  }

  onMounted(load)

  return {
    items,
    create,
    remove,
    copy,
    formatDate,
  }
}
