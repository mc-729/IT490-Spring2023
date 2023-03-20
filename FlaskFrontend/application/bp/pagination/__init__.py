
from flask import Blueprint, render_template, request, url_for
bp_pagination = Blueprint('pagination', __name__, template_folder='templates')
@bp_pagination.route('/Paginate', methods=['GET','POST'])
def render_pagination(pagination, endpoint, page):
    num_pages = pagination.get('total_pages')
    current_page = pagination.get('current_page')
    prev_page = pagination.get('prev_page')
    next_page = pagination.get('next_page')
    has_prev = pagination.get('has_prev')
    has_next = pagination.get('has_next')

    url_for_kwargs = request.view_args.copy()
    url_for_kwargs.update(request.args)

    pagination_links = []

    if has_prev:
        url_for_kwargs['page'] = prev_page
        prev_link = url_for(endpoint, **url_for_kwargs)
        pagination_links.append({'text': 'Previous', 'url': prev_link, 'is_active': False})
    else:
        pagination_links.append({'text': 'Previous', 'url': '#', 'is_active': False, 'is_disabled': True})

    for i in range(1, num_pages + 1):
        url_for_kwargs['page'] = i
        page_link = url_for(endpoint, **url_for_kwargs)
        pagination_links.append({'text': str(i), 'url': page_link, 'is_active': i == current_page})

    if has_next:
        url_for_kwargs['page'] = next_page
        next_link = url_for(endpoint, **url_for_kwargs)
        pagination_links.append({'text': 'Next', 'url': next_link, 'is_active': False})
    else:
        pagination_links.append({'text': 'Next', 'url': '#', 'is_active': False, 'is_disabled': True})

    return render_template('pagination.html', pagination_links=pagination_links, num_pages=num_pages, next_page=next_page)

