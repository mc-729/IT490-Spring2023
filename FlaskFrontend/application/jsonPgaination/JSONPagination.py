class JSONPagination:
    def __init__(self, items, page, per_page):
        self.items = items
        self.page = page
        self.per_page = per_page
        self.total = len(items)

    @property
    def pages(self):
        return -(-self.total // self.per_page)

    @property
    def has_prev(self):
        return self.page > 1

    @property
    def has_next(self):
        return self.page < self.pages

    @property
    def prev_num(self):
        return self.page - 1 if self.has_prev else None

    @property
    def next_num(self):
        return self.page + 1 if self.has_next else None

    def get_page_items(self):
        start = (self.page - 1) * self.per_page
        end = start + self.per_page
        return self.items[start:end]
