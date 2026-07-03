document.addEventListener('DOMContentLoaded', () => {

  const selector = '.acf-postbox.postbox'
  const tabSelector = '.acf-tab-group a, .acf-tab-button'
  const postId = document.getElementById('post_ID')?.value || window.location.pathname + window.location.search
  const storageKey = `bbcLandingAdminState:${postId}`

  function readState() {
    try {
      const value = window.localStorage.getItem(storageKey)
      return value ? JSON.parse(value) : null
    } catch (e) {
      return null
    }
  }

  function writeState(state) {
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(state))
    } catch (e) {
    }
  }

  function boxKey(box) {
    const title = box.querySelector('.postbox-header h2, .hndle, h2')?.textContent?.trim() || ''
    return box.id || title
  }

  function tabLabel(tab) {
    return tab.textContent.replace(/\s+/g, ' ').trim()
  }

  function openBox(box) {
    box.classList.remove('closed')

    if (window.jQuery) {
      window.jQuery(box).find('.inside').stop(true, true).show()
      return
    }

    const inside = box.querySelector('.inside')
    if (inside) inside.style.display = ''
  }

  function closeBox(box) {
    box.classList.add('closed')

    if (window.jQuery) {
      window.jQuery(box).find('.inside').stop(true, true).slideUp(150)
      return
    }

    const inside = box.querySelector('.inside')
    if (inside) inside.style.display = 'none'
  }

  function activeTab(box) {
    return box.querySelector('.acf-tab-group li.active a, .acf-tab-group a.-active, .acf-tab-button.-active, .acf-tab-button.active, .bbc-acf-tab-active')
  }

  function syncActiveTabs() {
    document.querySelectorAll('.bbc-acf-tab-active').forEach(tab => {
      tab.classList.remove('bbc-acf-tab-active')
      tab.removeAttribute('aria-current')
    })

    document.querySelectorAll(selector).forEach(box => {
      const tab = activeTab(box)
      if (!tab) return

      tab.classList.add('bbc-acf-tab-active')
      tab.setAttribute('aria-current', 'page')
    })
  }

  function saveState() {
    const previous = readState() || {}
    const state = {
      openBoxes: [],
      tabs: previous.tabs || {}
    }

    document.querySelectorAll(selector).forEach(box => {
      const key = boxKey(box)
      if (!key) return

      if (!box.classList.contains('closed')) {
        state.openBoxes.push(key)
      }

      const tab = activeTab(box)
      if (tab) {
        state.tabs[key] = tabLabel(tab)
      }
    })

    writeState(state)
  }

  function closeAllInitially() {
    document.querySelectorAll(selector).forEach(box => {
      if (!box.classList.contains('closed')) {
        closeBox(box)
      }
    })
  }

  function restoreBoxes(state) {
    if (!state) {
      closeAllInitially()
      return
    }

    document.querySelectorAll(selector).forEach(box => {
      const key = boxKey(box)
      if (key && state.openBoxes?.includes(key)) {
        openBox(box)
      } else {
        closeBox(box)
      }
    })
  }

  function restoreTabs(state) {
    if (!state?.tabs) return

    document.querySelectorAll(selector).forEach(box => {
      const key = boxKey(box)
      const label = key ? state.tabs[key] : ''
      if (!label) return

      const tab = Array.from(box.querySelectorAll(tabSelector)).find(item => tabLabel(item) === label)
      if (tab) tab.click()
    })
  }

  function init() {
    const state = readState()

    restoreBoxes(state)
    restoreTabs(state)
    syncActiveTabs()
    saveState()
  }

  window.setTimeout(init, 120)

  document.addEventListener('click', (e) => {
    const tab = e.target.closest(tabSelector)

    if (tab) {
      window.setTimeout(() => {
        syncActiveTabs()
        saveState()
      }, 80)
      return
    }

    const toggle = e.target.closest('.acf-postbox .handlediv, .acf-postbox .hndle, .acf-postbox .postbox-header')
    if (!toggle) return

    const box = toggle.closest(selector)
    if (!box) return

    window.setTimeout(() => {
      if (!box.classList.contains('closed')) {
        document.querySelectorAll(selector).forEach(other => {
          if (other !== box && !other.classList.contains('closed')) {
            closeBox(other)
          }
        })
      }

      syncActiveTabs()
      saveState()
    }, 80)
  })

  const form = document.getElementById('post')
  form?.addEventListener('submit', saveState)

  if (window.acf?.addAction) {
    window.acf.addAction('ready append', () => {
      syncActiveTabs()
      saveState()
    })
  }

})
