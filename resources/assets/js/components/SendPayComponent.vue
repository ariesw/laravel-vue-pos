<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Send Money To {{user.name}}</div>

                    <div class="panel-body">
                        <div class="form-group">
                          <input class="form-control" type="number" min="1.00" max="2000.00" placeholder="Amount To Send" v-model="amount">
                        </div>
                        <div class="form-group">
                          <input class="form-control" type="text"  placeholder="Note" v-model="note">
                        </div>
                        <div class="form-group">
                          <button v-on:click="sendMoney()" class="btn btn-primary">Pay <span class="glyphicon glyphicon-credit-card"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data(){
        return{
          amount: null,
          user: {},
          note: '',
          paymentRequest:null
        }
        },
        created(){
          this.user= JSON.parse(this.userObject);
          this.note = "Payment for " + this.user.name;
        },
        methods: {
        sendMoney: function(){
          const supportedPaymentMethods = [
            {
              supportedMethods: 'basic-card',
            }
          ];
          const paymentDetails = {
            total: {
              label: this.note,
              amount:{
                currency: 'USD',
                value: this.amount
              }
            }
          };
          // Options isn't required.
          const options = {};

          this.paymentRequest = new PaymentRequest(
            supportedPaymentMethods,
            paymentDetails,
            options
          );
          var that = this;
          this.paymentRequest.show().then(pay => {
            console.log(pay);
            axios.post('/api/send-payment/'+that.user.id,{pay:pay,amount:that.amount*100,note:that.note}).then(data => {
              alert('Payment Sent!');
            });
            return pay.complete();
          }).catch(err =>{
            console.log(err);
          });

        },
      },
      props: ['userObject']
        }

</script>
