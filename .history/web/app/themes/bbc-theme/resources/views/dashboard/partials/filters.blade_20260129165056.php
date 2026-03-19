<form method="get" class="flex flex-wrap gap-4">

  <select name="market" class="rounded-lg border px-4 py-2">
    <option value="">Alle Märkte</option>
    <option value="nasdaq">NASDAQ</option>
    <option value="metals">Edelmetalle</option>
    <option value="mining">Minen</option>
  </select>

  <select name="order" class="rounded-lg border px-4 py-2">
    <option value="desc">Neueste zuerst</option>
    <option value="asc">Älteste zuerst</option>
  </select>

  <button
    type="submit"
    class="px-5 py-2 rounded-lg bg-dashboard-accent text-white">
    Filtern
  </button>

</form>
