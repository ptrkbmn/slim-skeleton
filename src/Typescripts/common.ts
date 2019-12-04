document.addEventListener('DOMContentLoaded', function () {
});

class Helpers {
  static icon(name: string): HTMLElement {
    let i = document.createElement("i");
    i.classList.add("fas", "fa-" + name);
    return i;
  }
}