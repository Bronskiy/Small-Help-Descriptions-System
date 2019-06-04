'use strict';

const e = React.createElement;
const API_GET = '/phonebook/get';
const API_ADD = '/phonebook/add';
const API_EDIT = '/phonebook/edit';
const API_DELETE = '/phonebook/delete';

// create new contact
class NewContact extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      id:'',
      fname: '',
      lname: '',
      phone: '',
      mailSent: false,
      error: null
    }
  }

  handleFormSubmit = e => {
    e.preventDefault();
    axios({
        method: 'post',
        url: `${API_ADD}`,
        headers: { 'content-type': 'application/json' },
        data: this.state
      })
    .then(result => {
      this.setState( {
        mailSent: result.data.sent,
        id: result.data.last_id
      })
      //add contact to the table
      this.props.onContactTableAdd(this.state);
    })
    .catch(error => this.setState( { error: error.message } ));
  };

  render() {
    return (

      <div className="NewContact col-md-8">
        <hr class="mb-4"/>
        <form class="needs-validation" action="#">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="firstName">First name</label>
              <input type="text" class="form-control" id="firstName" placeholder=""
                  value={this.state.fname }
                  onChange={e => this.setState({ fname: e.target.value })}
                   />
            </div>
                <div class="col-md-6 mb-3">
                  <label for="lastName">Last name</label>
                  <input type="text" class="form-control" id="lastName" placeholder=""
                  value={this.state.lname}
                  onChange={e => this.setState({ lname: e.target.value })}
                  />
                </div>
              </div>

              <div class="mb-3">
                <label for="address">Phone</label>
                <input type="phone" class="form-control" id="dateofbirth" placeholder=""
                value={this.state.phone}
                onChange={e => this.setState({ phone: e.target.value })}
                 />
              </div>

              <hr class="mb-4"/>
              <input class="btn btn-primary btn-lg btn-block" type="submit" onClick = {e => this.handleFormSubmit(e)}  value="Add Contact" />
              <div class="contact-alerts">
                {this.state.mailSent  &&
                  <div className="alert alert-primary">Contact added.</div>
                }
                {this.state.error  &&
                  <div className="alert alert-danger">Sorry we had some problems.</div>
                }
              </div>
            </form>
      </div>
    );
  }
}

class Contacts extends React.Component {

  constructor(props) {
    super(props);

    this.state = {};
    this.state.filterText = "";
    this.state.contacts = [];

  }

  componentDidMount() {
      fetch(API_GET)
      .then(response =>  response.json())
      .then(resData => {
         this.setState({ contacts: resData });
      })
  }

  handleUserInput(filterText) {
    this.setState({filterText: filterText});
  };

  handleRowDel(contact) {
    axios({
        method: 'post',
        url: `${API_DELETE}`,
        headers: { 'content-type': 'application/json' },
        data: contact
      })
    .then(result => {
      this.setState( {
        mailSent: result.data.sent
      })
    })
    .catch(error => this.setState( { error: error.message } ));

    var index = this.state.contacts.indexOf(contact);
    this.state.contacts.splice(index, 1);
    this.setState(this.state.contacts);
  };

  handleRowEdit(contact) {
    if (contact) {
    axios({
        method: 'post',
        url: `${API_EDIT}`,
        headers: { 'content-type': 'application/json' },
        data: contact
      })
    .then(result => {
      this.setState( {
        mailSent: result.data.sent
      })
    })
    .catch(error => this.setState( { error: error.message } ));
  } else {
    this.setState( {
      edit: true
    })
  }

  };

  handleNewContactTable(contact){
   var item = {
      id: contact.id,
      contact_f_name: contact.fname,
      contact_l_name: contact.lname,
      contact_phone: contact.phone
    };

    this.state.contacts.push(item);
    this.setState(this.state.contacts);
  };

  handleContactTable(evt) {
    var item = {
      id: evt.target.id,
      name: evt.target.name,
      value: evt.target.value,
      edit: evt.target.edit
    };
  var contacts = this.state.contacts.slice();
  var newContacts = contacts.map(function(contact) {
    for (var key in contact) {
      if (key == item.name && contact.id == item.id) {
        contact[key] = item.value;
      }
    }
    return contact;
  });
    this.setState({contacts:newContacts});
  };

  render() {

    return (
      <div>
        <SearchBar filterText={this.state.filterText} onUserInput={this.handleUserInput.bind(this)}/>
        <ContactTable onContactCellUpdate={this.handleContactTable.bind(this)} onRowDel={this.handleRowDel.bind(this)} onRowEdit={this.handleRowEdit.bind(this)} contacts={this.state.contacts} filterText={this.state.filterText}/>
        <NewContact onContactTableAdd={this.handleNewContactTable.bind(this)} newContact={this.state.newContact}/>
      </div>
    );
  }
}

class SearchBar extends React.Component {
  handleChange() {
    this.props.onUserInput(this.refs.filterTextInput.value);
  }
  render() {
    return (
      <div class="search-area">
        <input type="text" placeholder="Search by first name" class="form-control" value={this.props.filterText} ref="filterTextInput" onChange={this.handleChange.bind(this)}/>
      </div>
    );
  }
}

class ContactTable extends React.Component {

  render() {
    var onContactCellUpdate = this.props.onContactCellUpdate;
    var rowDel = this.props.onRowDel;
    var rowEdit = this.props.onRowEdit;
    var filterText = this.props.filterText.toLowerCase();
    var contact = this.props.contacts.map(function(contact) {
      if (contact.contact_f_name.toLowerCase().indexOf(filterText) === -1) {
        return;
      }
      return (<ContactRow onContactCellUpdate={onContactCellUpdate} contact={contact} onDelEvent={rowDel.bind(this)} onEditEvent={rowEdit.bind(this)} key={contact.id}/>)
    });
    return (
      <div>
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>First Name</th>
              <th>Second Name</th>
              <th>Phone</th>
              <th>Controls</th>
            </tr>
          </thead>
          <tbody>
            {contact}
          </tbody>
        </table>
      </div>
    );
  }
}

class ContactRow extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
    this.state.edit = false;
  }
  onDelEvent() {
    this.props.onDelEvent(this.props.contact);
  }
  onEditEvent() {
    if (this.state.edit) {
      this.state.edit = false;
      this.props.onEditEvent(this.props.contact);
    } else {
      this.state.edit = true;
      this.props.onEditEvent();
    }
  }
  render() {

    return (
      <tr className="itemRow">
        <EditableCell onContactCellUpdate={this.props.onContactCellUpdate} cellData={{
          edit: this.state.edit,
          type: "contact_f_name",
          value: this.props.contact.contact_f_name,
          id: this.props.contact.id
        }}/>
        <EditableCell onContactCellUpdate={this.props.onContactCellUpdate} cellData={{
          edit: this.state.edit,
          type: "contact_l_name",
          value: this.props.contact.contact_l_name,
          id: this.props.contact.id
        }}/>
        <EditableCell onContactCellUpdate={this.props.onContactCellUpdate} cellData={{
          edit: this.state.edit,
          type: "contact_phone",
          value: this.props.contact.contact_phone,
          id: this.props.contact.id
        }}/>
        <td className="del-cell">
          <div class="btn-group mr-2">
            <EditButton onButtonClick={this.onEditEvent.bind(this)}  cellData={{
              edit: this.state.edit
            }} />
            <button type="button" onClick={this.onDelEvent.bind(this)} class="btn btn-sm btn-outline-danger">Delete</button>
          </div>
        </td>
      </tr>
    );
  }
}

class EditableCell extends React.Component {

  render() {
    if(this.props.cellData.edit){
      return (
        <td>
          <input type='text' name={this.props.cellData.type} id={this.props.cellData.id} value={this.props.cellData.value} onChange={this.props.onContactCellUpdate}/>
        </td>
      );
    }else {
      return (
        <td>
          {this.props.cellData.value}
        </td>
      );
    }
  }
}

class EditButton extends React.Component {

  render() {
    if(this.props.cellData.edit){
      return (
        <button type="button" onClick={this.props.onButtonClick} class="btn btn-sm btn-outline-success">Save</button>
      );
    } else {
      return (
        <button type="button" onClick={this.props.onButtonClick} class="btn btn-sm btn-outline-secondary">Edit</button>
      );
    }
  }
}

if (document.querySelector('#container')) {
  ReactDOM.render(e(Contacts), document.querySelector('#container'));
}
