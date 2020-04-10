/*
- התחברות והרשאה של מנהל מחוז 
- קבלת הגיסון של המחוז
- בניית הטבלה המתאימה
- 



*/

 
var UsersByDistrict = React.createClass({

    getInitialState: function(){
        return {
            allUsers: null
        }
    },
    
    componentDidMount() {

        axios.get(
            '/wp-json/captaincore/v1/customers', {
                headers: {'X-WP-Nonce':wpReactLogin.nonceApi}
            })
            .then(response => {
                //console.log(response.data);
                this.setState({allUsers: response.data});
                // this.setState({allUsers: 'response'});
                // this.customers = response.data;
            });


    },
    render: function() {
        console.log(this.state.allUsers);
        if(this.state.allUsers !== null){
            // const users = JSON.parse(this.state.allUsers);
            // console.log(users);
            var printUsers = this.state.allUsers.map((user,i) => 
        <p>{i} : {user.first_name} {user.last_name} {user.phone} {user.district} {user.member_purchase_date} {user.member_expiry_date} {user.roles}</p>
            )
        }

        var ReactBsTable  = window.BootstrapTable;  
        var BootstrapTable = ReactBsTable.BootstrapTable;
        var TableHeaderColumn = ReactBSTable.TableHeaderColumn;

        var products = [{
            id: 1,
            name: "Product1",
            price: 120
        }, {
            id: 2,
            name: "Product2",
            price: 80
        }];
        
        return (
            
            <div>
<BootstrapTable data={products} striped={true} hover={true}>
      <TableHeaderColumn dataField="id" isKey={true} dataAlign="center" dataSort={true}>Product ID</TableHeaderColumn>
      <TableHeaderColumn dataField="name" dataSort={true}>Product Name</TableHeaderColumn>
      <TableHeaderColumn dataField="price" dataFormat={priceFormatter}>Product Price</TableHeaderColumn>
  </BootstrapTable>

            </div>
        );
         
    } 
        
    
});



 
ReactDOM.render(<BootstrapTable />,document.getElementById("users-by-district"));
