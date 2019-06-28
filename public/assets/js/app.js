'use strict';

const e = React.createElement;
const API_GET = '/phonebook/get';


class Descriptions extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      hits: []
    };
  }

  componentDidMount() {
    fetch(API_GET)
    .then(response =>  response.json())
    .then(data => this.setState({ hits: data }));
  }

  render() {
    const { hits } = this.state;
    return (
      <ul class="nav flex-column mb-2">
      {hits.map(hit =>
        <li class="nav-item" key={hit.id}>
          <a class="nav-link" href={"/"+hit.id}>{hit.user === 'admin' ? (<span><span class="badge badge-secondary">{hit.id}</span> | </span>) : (<span></span>)}{hit.subject}<span class="badge badge-pill badge-primary">{hit.hidden}</span></a>
        </li>
      )}
      </ul>
    );
  }
}


if (document.querySelector('#desc_menu')) {
  ReactDOM.render(e(Descriptions), document.querySelector('#desc_menu'));
}
