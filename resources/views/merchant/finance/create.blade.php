@extends('layouts.merchant')


@section('header')

<div>

    <h1 class="text-2xl font-black text-slate-800">
        Tambah Rekening Bank
    </h1>


    <p class="text-sm text-slate-500 mt-1">
        Rekening pencairan saldo merchant
    </p>

</div>

@endsection





@section('content')


<div class="max-w-xl mx-auto">



    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">



        {{-- TITLE --}}

        <div class="mb-6">


            <h2 class="text-xl font-black text-slate-800">

                Informasi Rekening

            </h2>



            <p class="text-sm text-slate-500 mt-2">

                Pastikan data rekening benar.
                Rekening tidak dapat diubah setelah disimpan.

            </p>


        </div>
        {{-- ERROR --}}

        @if(session('error'))

            <div class="mb-5 rounded-xl bg-rose-50 text-rose-600 p-4 text-sm font-bold">

                {{ session('error') }}

            </div>

        @endif





        {{-- SUCCESS --}}

        @if(session('success'))

            <div class="mb-5 rounded-xl bg-emerald-50 text-emerald-600 p-4 text-sm font-bold">

                {{ session('success') }}

            </div>

        @endif







        <form

            method="POST"

            action="{{ route('merchant.bank-account.store') }}"

            class="space-y-5"

        >


        @csrf






            {{-- BANK --}}

            <div>


                <label class="block text-sm font-bold text-slate-700 mb-2">

                    Nama Bank

                </label>


                <input


                    type="text"


                    name="bank_name"


                    value="{{ old('bank_name') }}"


                    placeholder="Contoh : BCA"


                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:outline-none"


                    required


                >


                @error('bank_name')

                    <p class="text-xs text-rose-500 mt-2">

                        {{ $message }}

                    </p>

                @enderror


            </div>







            {{-- NOMOR REKENING --}}

            <div>


                <label class="block text-sm font-bold text-slate-700 mb-2">

                    Nomor Rekening

                </label>



                <input


                    type="text"


                    name="account_number"


                    value="{{ old('account_number') }}"


                    placeholder="Masukkan nomor rekening"


                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:outline-none"


                    required


                >


                @error('account_number')

                    <p class="text-xs text-rose-500 mt-2">

                        {{ $message }}

                    </p>

                @enderror


            </div>









            {{-- PEMILIK --}}

            <div>


                <label class="block text-sm font-bold text-slate-700 mb-2">

                    Nama Pemilik Rekening

                </label>




                <input


                    type="text"


                    name="account_name"


                    value="{{ old('account_name') }}"


                    placeholder="Nama sesuai rekening"


                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:outline-none"


                    required


                >



                @error('account_name')

                    <p class="text-xs text-rose-500 mt-2">

                        {{ $message }}

                    </p>

                @enderror


            </div>








            {{-- WARNING --}}


            <div class="rounded-xl bg-amber-50 border border-amber-100 p-4">


                <div class="flex gap-3">


                    <i class="fa-solid fa-lock text-amber-500 mt-1"></i>



                    <p class="text-sm text-amber-700">


                        Rekening hanya dapat disimpan satu kali.
                        Jika ingin mengganti rekening,
                        silahkan hubungi Super Admin.


                    </p>


                </div>


            </div>









            {{-- BUTTON --}}


            <div class="flex gap-3 pt-3">



                <a

                    href="{{ route('merchant.finance.index') }}"

                    class="flex-1 text-center py-3 rounded-xl bg-slate-100 text-slate-700 font-black"

                >

                    Batal

                </a>






                <button


                    type="submit"


                    class="flex-1 py-3 rounded-xl bg-slate-900 text-white font-black hover:bg-slate-800 transition"


                >


                    Simpan Rekening


                </button>




            </div>




        </form>





    </div>



</div>



@endsection